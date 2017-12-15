<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/1
 * Time: 16:48
 */
namespace Home\Controller;

use Think\Controller;

class ChancesController extends Controller{

    public function create_chances(){

        $ch_name   = I('post.ch_name','');
        $c_name    = I('post.c_name','');
        $ch_date   = I('post.ch_date','');
        $ch_money  = I('post.ch_money','');
        $ch_desc   = I('post.ch_desc','');

        //组织sign
        $sign = '{ch_name:"'.$ch_name.'"},'.
            '{c_name:"'.$c_name.'"},'.
            '{ch_date:"'.$ch_date.'"},'.
            '{ch_money:"'.$ch_money.'"},'.
            '{ch_desc:"'.$ch_desc.'"},';

        $sign = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        //判断两个sign值是否相等

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=>'error','message'=>'非法操作');
            echo json_encode($returnMessage);
            exit();
        }

        //数据操作

        $data['u_id'] = 1;  //写死得值
        $data['c_id'] = 1;  //写死得值

        $data['ch_name']      = $ch_name;
        $data['c_name']       = $c_name;
        $data['ch_date']      = $ch_date;
        $data['ch_money']     = $ch_money;
        $data['ch_desc']      = $ch_desc;
        $data['create_time']  = date("Y-m-d H:i:s", time());


        $res = M('chances')->add($data);


        if ($res){
            $returnMessage = array('code'=> 'success', 'message' => '添加成功');
            echo json_encode($returnMessage);
            exit;
        }else{
            $returnMessage = array('code'=> 'fail', 'message' => '添加失败');
            echo json_encode($returnMessage);
            exit;
        }
    }

    /**
     * 得到机会列表
     */
    public function get_chance()
    {
    	$userId = I('post.userId', 0);
    	$date   = I('post.date', date("Y-m", time()));
    	 
    	if ($userId == 0){
    		$returnMessage = array('code'=> 'fail', 'message' => '非法操作');
    		echo json_encode($returnMessage);
    		exit;
    	}
    	 
    	$where['u_id']    = $userId;
    	$where['ch_date'] = array('like', $date.'%');
    	
        $chanceList = M('chanceslist_view')->where($where)->order("ch_date desc")->select();
        echo json_encode(array('dataValue'=>$chanceList));
    }

    public function get_one_chance(){

        $ch_id              = I('post.ch_id','');

        $condition['ch_id'] = $ch_id;


        $res = M('chances')->where($condition)->find();

        if ($res){
            echo json_encode(array('dataValue'=>$res));
        }else{
            echo null;
        }
    }

    public function edit_chance(){

        $ch_name   = I('post.ch_name','');
        $c_name    = I('post.c_name','');
        $ch_date   = I('post.ch_date','');
        $ch_money  = I('post.ch_money','');
        $ch_desc   = I('post.ch_desc','');
        $ch_id     = I('post.ch_id','');
        $c_id      = I('post.c_id','');
        $u_id      = I('post.u_id','');

        //组织sign
        $sign = '{ch_name:"'.$ch_name.'"},'.
            '{c_name:"'.$c_name.'"},'.
            '{ch_date:"'.$ch_date.'"},'.
            '{ch_money:"'.$ch_money.'"},'.
            '{ch_desc:"'.$ch_desc.'"},'.
             '{ch_id:"' . $ch_id. '"},'.
             '{c_id:"' . $c_id . '"},'.
             '{u_id:"' . $u_id . '"},';

        $sign = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        //判断两个sign值是否相等

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=>'error','message'=>'非法操作');
            echo json_encode($returnMessage);
            exit();
        }

        //数据操作
        $data['ch_name']      = $ch_name;
        $data['c_name']       = $c_name;
        $data['ch_date']      = $ch_date;
        $data['ch_money']     = $ch_money;
        $data['ch_desc']      = $ch_desc;

        $condition['ch_id'] = $ch_id;
        $condition['c_id']  = $c_id;
        $condition['u_id']  =$u_id;

        $res = M('chances')->where($condition)->save($data);

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

    public function delete_chance(){
        $ch_id   = I('post.ch_id');

        $sign    = '{ch_id:"'.$ch_id . '"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($signVal != $_POST['signVal']){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        $condition['ch_id'] = $ch_id;

        $res = M('chances')->where($condition)->delete();

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
}
