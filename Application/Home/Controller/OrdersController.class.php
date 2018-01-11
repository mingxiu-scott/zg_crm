<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17/12/1
 * Time: 下午1:29
 */

namespace Home\Controller;
use Think\Controller;

class OrdersController extends Controller{

    /**
     * 录理财
     */

	public function create_order()
	{
		//获取前段数据
		$o_gettime     = I('post.o_gettime','');
		$o_customName  = I('post.o_customName','');
		$o_name        = I('post.o_name','');
		$o_money       = I('post.o_money','');
		$o_cycle       = I('post.o_cycle','');
		$o_endTime     = I('post.o_endTime','');
		$o_returnMoney = I('post.o_returnMoney','');
		$o_welfare     = I('post.o_welfare','');
		$o_remark      = I('post.o_remark','');
        $c_idcard      = I('post.c_idcard', '');
        $c_bankname    = I('post.c_bankname', '');
        $c_bankcard    = I('post.c_bankcard', '');


		//组织sign
		$sign = '{o_gettime:"'.$o_gettime.'"},'.
				'{o_customName:"'.$o_customName.'"},'.
				'{o_name:"'.$o_name.'"},'.
				'{o_money:"'.$o_money.'"},'.
				'{o_cycle:"'.$o_cycle.'"},'.
				'{o_endTime:"'.$o_endTime.'"},'.
				'{o_returnMoney:"'.$o_returnMoney.'"},'.
				'{o_welfare:"'.$o_welfare.'"},'.
				'{o_remark:"'.$o_remark.'"},'.
                '{c_idcard:"'.$c_idcard.'"},'.
                '{c_bankname:"'.$c_bankname.'"},'.
                '{c_bankcard:"'.$c_bankcard.'"},';

		$sign    = $sign.C('SIGNCODE');

		$signVal = md5($sign);

		if ($_POST['signVal'] != $signVal)
		{
			$returnMessage = array('code'=> 'error', 'message' => '非法操作');
			echo json_encode($returnMessage);
			exit;
		}

		//验证token

		//操作数据
		$data['c_id']           = I('customId',1);
		$data['u_id']           = I('userId',1);
		$data['s_id']           = I('storId',1);
		$data['o_date']         = $o_gettime;
		$data['g_id']           = I('goodId',1);
		$data['o_money']        = $o_money;
		$data['o_days']         = $o_cycle;
		$data['o_return_date']  = $o_endTime;
		$data['o_return_money'] = $o_returnMoney;
		$data['o_welfare']      = $o_welfare;
		$data['o_desc']         = $o_remark;
		$data['create_time']    = date("Y-m-d H:i:s", time());
        $data['g_name']         = $o_name;
        //添加部分开始
       $whereCustom['c_id']        = I('customId',1);
        $data2['c_idcard']     = $c_idcard;
        $data2['c_bankname']   = $c_bankname;
        $data2['c_bankcard']   = $c_bankcard;
        //添加部分结束

 		$Order = M("orders"); // 实例化Order对象
		$res = $Order->data($data)->add();

		//添加部分开始
		$Custom = M("customs");//实例化Customs对象
        $res2 = $Custom->where($whereCustom)->save($data2);
        //添加部分结束

        if(!empty($o_welfare)){
            $res3 = M('welfare')->where('w_id='.$o_welfare)->setDec('w_number',1);
        }

		if($res)
		{
		    $returnMessage = array('code'=> 'success', 'message' => '添加产品成功了',);
		    echo json_encode($returnMessage);
		    exit;
		}else{
		    $returnMessage = array('code'=> 'fail', 'message' => '添加产品失败，请重试');	
		    echo json_encode($returnMessage);
		    exit;
		}
    }

    /**
     * 得到 理财列表 
     */
    public function getOrderLists()
    {

		$token = I('post.tokenVal','');
		$u_id  = I('post.select_uid', 0);
		$date  = I('post.date', date("Y-m", time()));
		$uName = I('post.u_name', '');
		
		if (!empty($uName)){
			$whereOrderList['c_name'] = array('like', '%'.$uName.'%');
		}
		
		$whereOrderList['u_id']   = $u_id;
		$whereOrderList['o_date'] = array('like', $date.'%');		
        $orderView = M('orderslist_view');
        $orderList = $orderView->where($whereOrderList)->order('o_date desc')->select();
        
        if ($orderList) 
        {
            echo json_encode($orderList);
            exit;
        } else {
            $returnMessage = array('code' => 'fail', 'message' => '无结果');
            echo json_encode($returnMessage);
            exit;
        }
    }

    /**
     * 返回 理财详情
     */
    public function getOrderListInfo()
    {

        $o_id = I('post.o_id', 0);
        
        if ($o_id == 0)
        {
        	$returnMessage = array('code' => 'fail', 'message' => '非法操作');
        	echo json_encode($returnMessage);
        	exit;
        }
        
        $whereOrder['o_id'] = $o_id;
        $orderView = M('orderslist_view');
        $orderInfo = $orderView->where($whereOrder)->find();


        if (!empty($orderInfo['o_welfare'])){
            $welfare_id = M('welfare')->where('w_id='.$orderInfo['o_welfare'])->find();
            $orderInfo = $orderInfo + $welfare_id;
        }

        if ($orderInfo) 
        {
            echo json_encode($orderInfo);
            exit;
        } else {
        	
            $returnMessage = array('code' => 'fail', 'message' => '查询失败，请刷新一下');
            echo json_encode($returnMessage);
            exit;
        }
    }

    public function editOrderList()
    {

        $o_date         = I('post.o_date', '');
        $g_id           = I('post.g_id', '');
        $o_money        = I('post.o_money', '');
        $o_return_money = I('post.o_return_money', '');
        $c_id           = I('post.c_id', '');
        $u_id           = I('post.u_id', '');
        $where['o_id']  = I('post.o_id', '');

        //组织sign
        $sign = '{o_date:"' . $o_date . '"},' .
            	'{g_id:"' . $g_id . '"},' .
            	'{o_money:"' . $o_money . '"},' .
            	'{o_return_money:"' . $o_return_money . '"},' .
            	'{c_id:"' . $c_id . '"},' .
            	'{u_id:"' . $u_id . '"},' .

		$sign = $sign . C('SIGNCODE');
        $signVal = md5($sign);

        //验证sign
        if ($_POST['signVal'] != $signVal)
        {
            $returnMessage = array('code' => 'error', 'message' => '非法操作');
            echo json_encode($returnMessage);
            exit;
        }

        //数据
        $data['o_date']         = $o_date;
        $data['g_id']           = $g_id;
        $data['o_money']        = $o_money;
        $data['o_return_money'] = $o_return_money;
        $data['c_id']           = $c_id;
        $data['u_id']           = $u_id;

        //
        $order = M('orders');
        $res = $order->where($where)->save($data);
        if ($res) {
            $returnMessage = array('code' => 'success', 'message' => '更新成功',);
            echo json_encode($returnMessage);
            exit;
        } else {
            $returnMessage = array('code' => 'fail', 'message' => '更新失败',);
            echo json_encode($returnMessage);
            exit;
        }
    }

    /**
     * 得到回款列表数据
     */
    public function getReturn()
    {

    	$u_id   = I('userId', 0);
    	$c_name = I('search_cname', '');
    	$date   = I('post.date', date("Y-m", time()));
    	
    	if ($u_id == 0){
    		$returnMessage = array('code'=> 'error', 'message' => '非法操作');
    		echo json_encode($returnMessage);
    		exit;
    	}
    	
   		if (!empty($c_name)){
    		$whereOrderList['c_name'] = array('like', '%'.$c_name.'%');
    	}

        $whereOrderList['u_id']   = $u_id;
		$whereOrderList['o_date'] = array('like', $date.'%');		
        $orderView = M('orderslist_view');
        $orderList = $orderView->where($whereOrderList)->order('o_return_date desc')->select();

        if ($orderList) {
            echo json_encode($orderList);
            exit;
        } else {
            $returnMessage = array('code' => 'fail', 'message' => '查询失败');
            echo json_encode($returnMessage);
            exit;
        }
    }

    public function getReturnOne()
    {

        $data['o_id'] = I('post.o_id', '');
        $order = M('orders');
        $res = $order->join('crm_customs ON crm_orders.c_id = crm_customs.c_id')->where($data)->find();
        if ($res) {
            echo json_encode($res);
            exit;
        } else {
            $returnMessage = array('code' => 'fail', 'message' => '');
            echo json_encode($returnMessage);
            exit;
        }
    }

	public function sureReturn()
	{
	
		$o_id  = I('post.o_id','5');
		$order = M('orders');
		$data['o_id'] = $o_id;
		
		$ress = $order->where($data)->getField('o_id,state');
		
		if($ress["$o_id"] == 0){
			
			$data['state'] = 1;
			$res = $order->save($data);
			
			if($res){
				$returnMssage = array('code' => 'success','message' => '回款成功');
				echo json_encode($returnMssage);
				exit();
			}else{
				$returnMssage = array('code' => 'fail','message' => '回款失败');
				echo json_encode($returnMssage);
				exit();
			}
		}else{
			$returnMssage = array('code' => 'fail','message' => '已回款过，不能再次回款');
			echo json_encode($returnMssage);
			exit();
		}
	
	}

	/**
	 * 得到报表信息
	 */
	public function getReportJson()
	{
		$userId = I('post.userId', 0);
		$date   = I('post.date', date("Y-m", time()));
		 
		if ($userId == 0){
			$returnMessage = array('code'=> 'fail', 'message' => '非法操作');
			echo json_encode($returnMessage);
			exit;
		}
		
		// 客户数
		$whereCustomNum['u_id'] = $userId;
		$customNum = M('customs')->where($whereCustomNum)->count(); 
		
		// 理财总额,  
		$whereOrderMoney['u_id']   = $userId;
		$whereOrderMoney['o_date'] = array('like', $date.'%');
		$o_moneyArray = M('orders')->field('sum(o_money) as money')->where($whereOrderMoney)->group('u_id')->select();

		// 成交单数
		$orderCount = M('orders')->where($whereOrderMoney)->count();
		
		//回款总额 
		$whereReturnMoney['u_id']   = $userId;
		$whereReturnMoney['o_return_date'] = array('like', $date.'%');
		$r_moneyArray = M('orders')->field('sum(o_return_money) as return_money')->where($whereReturnMoney)->group('u_id')->select();
		
		// 跟进次数
		$whereFollows['u_id'] = $userId;
		$whereFollows['fl_date'] = array('like', $date.'%');
		$followsCount = M('follows_log')->where($whereFollows)->count();
		
		$array['customNum']    = $customNum;
		$array['orderCount']   = $orderCount;
		$array['o_money']      = $o_moneyArray[0]['money'];
		$array['return_money'] = $r_moneyArray[0]['return_money'];
		$array['followsCount'] = $followsCount;
		
		echo json_encode($array);
	}

    /*
  *获取年月列表
  */
    /*
    *获取年月列表
    */
    public function getYearMonthList(){

//        if(I('post.userId')){

//            $userId = I('post.userId');

            $order = M('orders');

//            $array['u_id'] = $userId;
             //$array['u_id'] = 1;

            $res = $order->field('o_id','o_date')->select();

//            $arr = array();



            echo json_encode($res);
            exit();
        }
//    }
}