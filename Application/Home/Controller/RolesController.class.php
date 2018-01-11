<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 10:15
 */
namespace Home\Controller;

use Think\Controller;


class RolesController extends Controller{

    public function getRoles(){

        $c_name = I('post.search_cname', '');
        $userID = I('post.userID','');

        if (!empty($c_name)) {
            $whereCustom['r_name'] = array('like', '%'.$c_name.'%');
        }

        if (!empty($userID) && $userID != '1'){

            $condition['u_id'] = $userID;

            $resUser = M('role_user')->where($condition)->find();

            $whereCustom['parent_id'] = $resUser['r_id'];
        }

        $res = M('roles')->where($whereCustom)->select();

        echo json_encode(array('dataValue'=>$res));
    }

    public function create_role(){

        $pr_id = I('post.pr_id','');
        $r_name = I('post.r_name','');
        
        $sign  = '{pr_id:"' . $pr_id . '"},' .
            '{r_name:"' . $r_name . '"},';

        $sign    = $sign.C('SIGNCODE');

        $signVal = md5($sign);

        if ($_POST['signVal'] != $signVal){
            $returnMessage = array('code'=> 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        $data['r_id'] = '1001';
        $data['parent_id'] = $pr_id;
        $data['r_name'] = $r_name;
        $data['status'] = 1;

        $condition['parent_id'] = $pr_id;

        $res = M('roles')->where($condition)->max('r_id');

        if (!$res){
            $max_id = $pr_id.'01';
        }else{
            $max_id = $res + 1;
        }

        $data['r_id'] = $max_id;

        $resInsert = M('roles')->add($data);

        if ($resInsert){
            $returnMessage = array('code'=> 'success', 'message' => '添加成功');
            echo json_encode($returnMessage);
            exit;
        }else{
            $returnMessage = array('code'=> 'fail', 'message' => '添加，请重试');
            echo json_encode($returnMessage);
            exit;
        }
    }
}