<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/1
 * Time: 15:58
 */

namespace Home\Controller;

use Think\Controller;

class TasksController extends Controller{

    /**
     * 写日志
     */
    public function create_task(){
        $t_name   = I('post.t_name','');
        $t_date   = I('post.t_date','');
        $c_name   = I('post.c_name','');
        $t_desc   = I('post.t_desc','');
        $t_status = I('post.t_status','');
        $c_id_in  = I('post.c_id','');
        $t_endtime= I('post.t_endtime','');
        $t_xiashu_id = I('post.xiashu_id');

       //组织sign
        $sign = '{t_name:"'.$t_name.'"},'.
            '{t_date:"'.$t_date.'"},'.
            '{c_name:"'.$c_name.'"},'.
            '{t_desc:"'.$t_desc.'"},'.
            '{t_status:"'.$t_status.'"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        //数据操作
        $new_t_status = 0;

        $u_id = I('userId','');

        $c_id = $c_id_in;

        $data['c_id'] = $c_id;
        $data['u_id'] = $t_xiashu_id;

        $data['create_time']  = date("Y-m-d H:i:s", time());
        $data['t_date']       = $t_date;
        $data['c_name']       = $c_name;
        $data['t_desc'] = $t_desc;
        $data['t_name'] = $t_name;
        $data['t_status'] = $new_t_status;
        $data['t_endtime'] = $t_endtime;

        $res = M('tasks')->add($data);

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
     * 获得日志内容
     */
    public function get_tasks(){

    	$userId   = I('post.userId', 0);
    	$date     = I('post.date', date("Y-m", time()));
        $t_status = I('post.t_status',2);


    	if ($userId == 0){
    		$returnMessage = array('code'=> 'fail', 'message' => '非法操作');
    		echo json_encode($returnMessage);
    		exit;
    	}


    	if ($t_status != 2){
    	    $where['t_status'] = $t_status;
        }
    	
    	$where['u_id']    = $userId;
    	$where['t_date'] = array('like', $date.'%');

        $taskList = M('taskslist_view')->where($where)->order("t_date desc")->select();
        echo json_encode(array('dataValue'=>$taskList));
    }

    public function get_one_task(){

        $t_id = I('post.t_id','');

        $condition = array('t_id'=> $t_id);

        $task = M('tasks')->where($condition)->find();

        echo json_encode(array('dataValue'=>$task));

    }

    public function edit_task(){

        $t_id         = I('post.t_id');
        $u_id         = I('post.u_id');
        $c_id         = I('post.c_id');
        $t_name       = I('post.t_name','');
        $t_date       = I('post.t_date','');
        $c_name       = I('post.c_name','');
        $t_desc       = I('post.t_desc','');
        $t_status     = I('post.t_status','');
        $t_finishtime = I('post.t_finishtime','');
        $t_feedback   = I('t_feedback','');

        //组织sign
        $sign = '{t_name:"'.$t_name.'"},'.
            '{t_date:"'.$t_date.'"},'.
            '{c_name:"'.$c_name.'"},'.
            '{t_desc:"'.$t_desc.'"},'.
            '{t_status:"'.$t_status.'"},'.
            '{t_id:"'.$t_id.'"},'.
            '{u_id:"'.$u_id.'"},'.
            '{c_id:"'.$c_id.'"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }
        if($t_status == '未完成'){
            $new_t_status = 0;
        }else{
            $new_t_status = 1;
        }
        $condition['t_id'] = $t_id;
        $condition['c_id'] = $c_id;
        $condition['u_id'] = $u_id;

        $data['t_date']       = $t_date;
        $data['c_name']       = $c_name;
        $data['t_desc']       = $t_desc;
        $data['t_name']       = $t_name;
        $data['t_status']     = $new_t_status;
        $data['t_feedback']   = $t_feedback;
        $data['t_finishtime'] = $t_finishtime;

        $res = M('tasks')->where($condition)->save($data);

        if ($res){
            $returnMessage = array('code'=> 'success', 'message' => '更新成功');
            echo json_encode($returnMessage);
            exit;
        }else{
            $returnMessage = array('code'=> 'fail', 'message' => '更新失败，请重试');
            echo json_encode($returnMessage);
            exit;
        }
    }

    public function delete_task(){
        $t_id    = I('post.t_id');

        $sign    =  '{t_id:"'.$t_id.'"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        $condition['t_id'] = $t_id;

        $res = M('tasks')->where($condition)->delete();

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

};
