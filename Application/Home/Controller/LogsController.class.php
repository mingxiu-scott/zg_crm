<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/1
 * Time: 11:23
 */

namespace Home\Controller;

use Think\Controller;

class LogsController extends Controller{


    public function index() {
         print_r(M('customs')->select());
    }
    
    /**
     *  录日志
     */

    public function create_log()
    {
        $l_date = I('post.l_date','');
        $l_desc = I('post.l_desc','');
        $u_id   = I('post.userId','');


        $sign = '{l_date:"'.$l_date.'"},'.
          '{l_desc:"'.$l_desc.'"},'.
          '{u_id:"'.$u_id.'"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($_POST['signVal'] != $signVal){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        //数据操作
        $data['u_id']         = $u_id;
        $data['l_date']       = $l_date;
        $data['l_desc']       = $l_desc;
        $data['create_time']  = date("Y-m-d H:i:s", time());

        $res = M('logs')->add($data);

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

    /**
     * 得到日志列表
     */
    public function get_logs()
    {

    	$userId = I('post.userId', 0);
    	$date   = I('post.date', date("Y-m", time()));
    	
    	if ($userId == 0){
    		$returnMessage = array('code'=> 'fail', 'message' => '非法操作');
    		echo json_encode($returnMessage);
    		exit;
    	}
    	
    	$where['u_id']   = $userId;
    	$where['l_date'] = array('like', $date.'%');
        $followsLogList  = M('logslist_view')->where($where)->order("l_date desc")->select();


        echo json_encode(array('dataValue'=>$followsLogList));
    }

    public function get_one_log()
    {
        $l_id = I('post.l_id','');

        $condition['l_id'] = $l_id;

        $res = M('logs')->where($condition)->find();

        if ($res){
            echo json_encode(array('dataValue'=>$res));
        }else{
            echo null;
        }
    }

    public function delete_log(){
        $l_id = I('post.l_id');

        $sign =  '{l_id:"'.$l_id.'"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        $condition['l_id'] = $l_id;

        $res = M('logs')->where($condition)->delete();

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

    public function edit_log_Info(){

        $l_date = I('post.l_date','');
        $l_desc = I('post.l_desc','');
        $l_id = I('post.l_id','');
        $u_id = I('post.u_id','');

        $sign = '{l_date:"'.$l_date.'"},'.
            '{l_desc:"'.$l_desc.'"},'.
            '{l_id:"'.$l_id.'"},'.
            '{u_id:"'.$u_id.'"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($_POST['signVal'] != $signVal){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        //数据操作

        $condition['l_id'] = $l_id;
        $condition['u_id'] = $u_id;

        $data['l_date']       = $l_date;
        $data['l_desc']       = $l_desc;

        $res = M('logs')->where($condition)->save($data);

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






