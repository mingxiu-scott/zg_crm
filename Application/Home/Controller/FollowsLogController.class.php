<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/5
 * Time: 8:45
 */
namespace Home\Controller;

use Think\Controller;

/**
 * Class FollowsLog
 * @package Home\Controller
 * 日志操作类
 */
class FollowsLogController extends Controller
{

    /**
     * 获得日志列表
     */
    public function get_follows_log()
    {
    	$u_id   = I('post.userId', 0);
    	$c_name = I('post.search_cname', '');
    	
    	if ($u_id == 0){
    		$returnMessage = array('code'=> 'error', 'message' => '非法操作');
    		echo json_encode($returnMessage);
    		exit;
    	}
    	
    	if (!empty($c_name)){
    		$whereFollows['c_name'] = array('like', '%'.$c_name.'%');
    	}
    	
    	$whereFollows['u_id'] = $u_id;    	
        $followsLogList = M('followslist_view')->where($whereFollows)->select();
        
        $array = array('dataValue'=>$followsLogList);
        echo json_encode($array);
    }

    public function delete_follows_log(){
        $fl_id   = I('post.fl_id');
        $sign    = '{fl_id:"'.$fl_id . '"},';
        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        $condition['fl_id'] = $fl_id;

        $res = M('follows_log')->where($condition)->delete();

        if ($res){
            $returnMessage = array('code'=> 'success', 'message' => '删除成功');
            echo json_encode($returnMessage);
            exit;
        }else{
            $returnMessage = array('code'=> 'fail', 'message' => '删除失败，请重试');
            echo json_encode($returnMessage);
            exit;
        }
    }

    public function get_one_follows(){
        $fl_id = I('post.fl_id','');

        $condition['fl_id'] = $fl_id;

        $res = M('follows_log')->where($condition)->find();

        if ($res){
            echo json_encode(array('dataValue'=>$res));
        }else{
            echo null;
        }
    }


    public function edit_follow_log(){

        $fl_id   = I('post.fl_id','');
        $c_name  = I('post.c_name','');
        $fl_date = I('post.fl_date');
        $fl_desc = I('post.fl_desc');
        $c_id    = I('post.c_id');
        $u_id    = I('post.u_id');

        $sign = '{c_name:"' . $c_name . '"},'. 
            '{fl_desc:"' . $fl_desc . '"},'.
            '{fl_date:"' . $fl_date . '"},'.
            '{fl_id:"' . $fl_id . '"},'.
            '{c_id:"' . $c_id . '"},'.
            '{u_id:"' . $u_id . '"},';

        $sign = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        $condition['fl_id'] = $fl_id;
        $condition['u_id']  = $u_id;
        $condition['c_id']  = $c_id;

        $data['fl_date']  = $fl_date;
        $data['fl_desc']  = $fl_desc;
        $data['c_name']   = $c_name;

        $res = M('follows_log')->where($condition)->save($data);

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

    public function create_followlog(){

        $f_gettime    = I('post.f_gettime', '');
        $c_name       = I('post.c_name', '');
        $f_content    = I('post.f_content', '');
        $u_id         = I('post.userId', 0);
        $customId     = I('post.customId', 0);


        $sign = '{f_gettime:"'.$f_gettime.'"},'.
            '{c_name:"'.$c_name.'"},'.
            '{f_content:"'.$f_content.'"},'.
            '{u_id:"'.$u_id.'"},'.
            '{customId:"'.$customId.'"},';

        $sign = $sign.C('SIGNCODE');


        $signVal = md5($sign);

        // 验证 sign
        if ($_POST['signVal'] != $signVal)
        {
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        //操作数据
        $data['c_id']        = $customId;
        $data['u_id']        = $u_id;
        $data['c_name']      = $c_name;
        $data['fl_date']     = $f_gettime;
        $data['fl_desc']     = $f_content;
        $data['create_time'] = date("Y-m-d H:i:s", time());

        $FollowsLog = M("follows_log"); // 实例化follow_log对象
        $res = $FollowsLog->data($data)->add();

        if($res){

            $returnMessage = array('code'=> 'success', 'message' => '添加跟进成功');

            echo json_encode($returnMessage);
            exit;
        }else{
            $returnMessage = array('code'=> 'fail', 'message' => '添加跟进失败，请重试');

            echo json_encode($returnMessage);
            exit;
        }

    }

}
