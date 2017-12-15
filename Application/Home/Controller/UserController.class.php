<?php
namespace Home\Controller;
use Think\Controller;

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
                $returnMessage = array('code'=> '2', 'message' => '登录成功','u_id'=>$usernameRes['u_id']);
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
}