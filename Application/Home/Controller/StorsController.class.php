<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/8
 * Time: 16:21
 */
namespace Home\Controller;

use Think\Controller;

class StorsController extends Controller{

    public function get_stors(){
        $res = M('stors')->select();
        echo json_encode(array('dataValue'=>$res));
    }
}