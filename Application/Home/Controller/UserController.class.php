<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;

class UserController extends Controller {

	public function login(){
        $username = I('post.username','');
        $password = I('post.password','');

        $sign =
            '{username:"' . $username . '"},' .
            '{password:"' . $password . '"},';

        $sign = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=>'error','message'=>'非法操作');
            echo json_encode($returnMessage);
            exit();
        }

        //验证用户名和密码

        $condition['u_username'] = $username;

        $usernameRes = M('users')->where($condition)->find();

        if ($usernameRes){
            if ($password != $usernameRes['u_password']){
                $returnMessage = array('code'=> '1', 'message' => '密码不正确');
            }else{
                $returnMessage = array('code'=> '2', 'message' => '登录成功','u_id'=>$usernameRes['u_id'],'r_id'=>$usernameRes['r_id']);
            }
            echo json_encode($returnMessage);
            exit;
        }else{
            $returnMessage = array('code'=> '0', 'message' => '该用户不存在');
            echo json_encode($returnMessage);
            exit;
        }
    }

	/**
	 * 得到下属列表
	 */
	public function getUsersListsJson()
	{
		$uid = I('post.userId', 1);
		$UserModel = M('users');

		$whereUserRid['u_id'] =  $uid;
		$r_id = $UserModel->where($whereUserRid)->getField('r_id');

		$whereUserList['r_id'] = array('like', $r_id.'%');
		$userList = $UserModel->field('u_id, u_name')->where($whereUserList)->select();

		$userArray = Array();

		foreach($userList as $key => $val){

			$u_id = $val['u_id'];
			$u_name = $val['u_name'];
			$userArray[$u_id] = $u_name;
		}

		echo json_encode($userArray);
	}

	public function getUsers(){

        $u_id = I('post.userId','1');

        $res = M('users')->where('u_id='.$u_id)->find();

        echo json_encode(array('dataValue'=>$res,'code'=>'success'));
    }

    public function changPass(){

        $u_id = I('post.userId','1');
        $old_pass = I('post.old_pass','');
        $new_pass = I('post.new_pass','');
        $check_pass = I('post.check_pass','');

        $sign = '{old_pass:"'.$old_pass.'"},'.
            '{new_pass:"'.$new_pass.'"},'.
            '{check_pass:"'.$check_pass.'"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($_POST['signVal'] != $signVal){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        $where['u_id']         = $u_id;
        $data['u_password']      = $new_pass;

        $message = M('users')->where($where)->find();

        if ($message['u_password'] != $old_pass){
            $returnMessage = array('code'=> 'fail', 'message' => '修改失败，请输入正确的密码');
            echo json_encode($returnMessage);
            exit();
        }

        $res = M('users')->where($where)->save($data);

        if ($res){
            $returnMessage = array('code'=> 'success', 'message' => '修改成功');
            echo json_encode($returnMessage);
            exit;
        }else{
            $returnMessage = array('code'=> 'fail', 'message' => '修改失败，请重试');
            echo json_encode($returnMessage);
            exit;
        }
    }

    public function getUsersListsJson2()
    {
        $uid = I('post.userId', 1);

        $xiashuID = I('post.xiashuID','');

        //添加部分开始
        //如果有下属姓名输入
        if(I('post.search_cname') && I('post.search_cname') != ''){
            $uname = I('post.search_cname');
            $whereUserList['u_name'] = array('like','%'.$uname.'%');
        }

        $UserModel = M('user_roles_view');

        $whereUserRid['u_id'] =  $uid;

        $r_id = $UserModel->where($whereUserRid)->getField('r_id');

        if ($xiashuID != 'test'){
            $whereUserList['r_id'] = array('like', $r_id.'_%');
        }

        $userList = $UserModel->distinct(true)->field('u_id, u_name')->where($whereUserList)->select();

        echo json_encode($userList);
    }

    public function create_user(){

        $u_name = I('post.u_name','');
        $u_username = I('post.u_username','');
        $u_telphone = I('post.u_telphone','');
        $u_age = I('post.u_age','');

        $sign =
            '{u_username:"'. $u_username . '"},'.
            '{l_name:"'. $u_name . '"},'.
            '{u_telphone:"'. $u_telphone . '"},'.
            '{u_age:"'. $u_age .'"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($_POST['signVal'] != $signVal){
                $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        $data['u_username'] = $u_username;
        $data['u_password'] = '123456';
        $data['u_telphone'] = $u_telphone;
        $data['u_name'] = $u_name;

        $res = M('users')->add($data);

        if ($res){
            $returnMessage = array('code'=> 'success', 'message' => '添加成功');
            echo json_encode($returnMessage);
            exit;
        }else{
            $returnMessage = array('code'=> 'fail', 'message' => '添加，请重试');
            echo json_encode($returnMessage);
            exit;
        }

    }

    public function create_user_role(){

        $u_id = I('post.u_id','');
        $s_id = I('post.s_id','');
        $r_id = I('post.r_id','');

        $sign =
            '{u_id:"' . $u_id . '"},' .
            '{s_id:"' . $s_id . '"},' .
            '{r_id:"' . $r_id . '"},' ;

        $sign    = $sign.C('SIGNCODE');


        $signVal = md5($sign);

        if ($_POST['signVal'] != $signVal){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        $data1['s_id'] = $s_id;

         $data['r_id'] = $r_id;
         $data['u_id'] = $u_id;

         $res2 = M('users')->where('u_id='.$u_id)->save($data1);
        $res = M('role_user')->add($data);

        if ($res){
            $returnMessage = array('code'=> 'success', 'message' => '添加成功');
            echo json_encode($returnMessage);
            exit;
        }else{
            $returnMessage = array('code'=> 'fail', 'message' => '添加失败，请重试');
            echo json_encode($returnMessage);
            exit;
        }
    }

    public function getSuborinateList(){

        $u_id = I('userId','1');
        $username = I('username','');
        $deleteId  = I('deleteId','');
        $whereUser = array();
        $whereUser['u_id'] = $u_id;
        $user = M('role_user');
        $res = $user->where($whereUser)->find();

        /********************************************************/

        /*删除某一位下属*/
        if($deleteId != ''){

            $data['ru_status'] = 0;
            $b = $user->where('r_id='.$deleteId)->save($data);
        }
        /********/

        $where = array();
        $where['ru_status'] = 1;
        $where['r_id'] = array('like', $res['r_id'].'_%');

        if ($username != '') {
            $where['u_name'] = array('like', "%".$username.'%');
        }

        $users = M('user_roles_view');
        $res2 = $users->where($where)->select();
        echo json_encode($res2);
        exit();
    }

    public function getAllUserList(){

        $username = I('username','');

        if ($username != '') {
            $where['u_name'] = array('like', $username.'%');
        }

        $user = M('users');

        $res = $user->where($where)->select();

        echo json_encode($res);

        exit();
    }

}