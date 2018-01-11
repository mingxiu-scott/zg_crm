<?php
namespace Home\Controller;
use Think\Controller;

class CustomController extends Controller {
	
	// 录客户
	public function create_custom() {
		
		$c_gettime  = I('post.c_gettime', '');
		$c_name     = I('post.c_name', '');
		$c_telphone = I('post.c_telphone', '');
		$c_idcard   = I('post.c_idcard', '');
		$c_bankname = I('post.c_bankname', '');
		$c_bankcard = I('post.c_bankcard', '');
		$c_sex      = I('post.c_sex', '');
		$c_address  = I('post.c_address', '');
		$c_called   = I('post.c_called', '');
		$c_age      = I('post.c_age', '');
		$c_birthday = I('post.c_birthday', '');
		$c_source   = I('post.c_source', '');
		$c_desc     = I('post.c_desc', '');		
	
		// 组织 sign
		$sign = '{c_gettime:"'.$c_gettime.'"},'.
            '{c_name:"'.$c_name.'"},'.
            '{c_telphone:"'.$c_telphone.'"},'.
            '{c_idcard:"'.$c_idcard.'"},'.
            '{c_bankname:"'.$c_bankname.'"},'.
            '{c_bankcard:"'.$c_bankcard.'"},'.
            '{c_sex:"'.$c_sex.'"},'.
            '{c_address:"'.$c_address.'"},'.
            '{c_called:"'.$c_called.'"},'.
            '{c_age:"'.$c_age.'"},'.
            '{c_birthday:"'.$c_birthday.'"},'.
            '{c_source:"'.$c_source.'"},'.
            '{c_desc:"'.$c_desc.'"}';
		
		$sign    = $sign.C('SIGNCODE');
		$signVal = md5($sign);
		
		// 验证 sign
		if ($_POST['signVal'] != $signVal) 
		{
			$returnMessage = array('code'=> 'error', 'message' => '非法操作');
			echo json_encode($returnMessage);
			exit;
		}
		
		// 验证token		
		$uid                  = I('userId', 0);
		$whereUser['u_id']    = $uid;
		$whereUser['u_state'] = 1;
		$token = M('users')->where($whereUser)->getField('token');
		$s_id  = 1;
		/*
		if ($_POST['tokenVal'] != $token)
		{
			$returnMessage = array('code'=> 'error', 'message' => '非法操作');
			echo json_encode($returnMessage);
			exit;
		}
		*/
		
		// 操作数据
		$data['c_name']       = $c_name;
		$data['c_sex']        = $c_sex;
		$data['c_telphone']   = $c_telphone;
		$data['c_idcard']     = $c_idcard;
		$data['c_bankname']   = $c_bankname;
		$data['c_bankcard']   = $c_bankcard;
		$data['c_address']    = $c_address;
		$data['c_called']     = $c_called;
		$data['c_age']        = $c_age;
		$data['c_birthday']   = $c_birthday;
		$data['c_source']     = $c_source;
		$data['c_desc']       = $c_desc;
		$data['c_gettime']    = $c_gettime;
		$data['s_id']         = $s_id;
		$data['create_u_id']  = I('userId', 0);
		$data['u_id']         = $uid;
		$data['create_time']  = date("Y-m-d H:i:s", time());
		
		$id = M('customs')->add($data);
		if ($id)
		{
			$returnMessage = array('code'=> 'success', 'message' => '添加成功');
			echo json_encode($returnMessage);
			exit;
		}
		else
		{
			$returnMessage = array('code'=> 'fail', 'message' => '添加失败，请重试');
			echo json_encode($returnMessage);
			exit;
		}
	}
	
	/**
	 * 获取客户列表
	 */
	public function getCustomsList()
	{
		$c_name = I('post.search_cname', '');
		$u_id   = I('post.userId', '');
		
		if (!empty($c_name)) {
			$whereCustom['c_name'] = array('like', '%'.$c_name.'%');
		}
	
		$whereCustom['u_id'] = $u_id;
		$customsList = M('customlist_view')->where($whereCustom)->select();

		$array = array('dataValue'=>$customsList);
		echo json_encode($array);		
	}	
	
	/**
	 * 获取客户详细信息
	 */
	public function getCustomsInfo()
	{
		$c_id = I('post.c_id', 0);
		$where['c_id'] = $c_id;
		$customInfo = M('customs')->where($where)->find();
		echo json_encode($customInfo);
	}
	
	/**
	 * 修改客户详情信息
	 */
	public function editCustomInfo()
	{
		$c_id       = I('post.c_id', '');
		
		if (empty($c_id)){
			$returnMessage = array('code'=> 'fail', 'message' => '非法操作');
			echo json_encode($returnMessage);
			exit;
		}
		
		$c_gettime  = I('post.c_gettime', '');
		$c_name     = I('post.c_name', '');
		$c_telphone = I('post.c_telphone', '');
		$c_idcard   = I('post.c_idcard', '');
		$c_bankname = I('post.c_bankname', '');
		$c_bankcard = I('post.c_bankcard', '');
		$c_sex      = I('post.c_sex', '');
		$c_address  = I('post.c_address', '');
		$c_called   = I('post.c_called', '');
		$c_age      = I('post.c_age', '');
		$c_birthday = I('post.c_birthday', '');
		$c_source   = I('post.c_source', '');
		$c_desc     = I('post.c_desc', '');
		
		// 组织 sign
		$sign = '{c_gettime:"'.$c_gettime.'"},'.
				'{c_name:"'.$c_name.'"},'.
				'{c_telphone:"'.$c_telphone.'"},'.
				'{c_idcard:"'.$c_idcard.'"},'.
				'{c_bankname:"'.$c_bankname.'"},'.
				'{c_bankcard:"'.$c_bankcard.'"},'.
				'{c_sex:"'.$c_sex.'"},'.
				'{c_address:"'.$c_address.'"},'.
				'{c_called:"'.$c_called.'"},'.
				'{c_age:"'.$c_age.'"},'.
				'{c_birthday:"'.$c_birthday.'"},'.
				'{c_source:"'.$c_source.'"},'.
				'{c_desc:"'.$c_desc.'"}';
		
		$sign    = $sign.C('SIGNCODE');
		$signVal = md5($sign);
		
		// 验证 sign
		if ($_POST['signVal'] != $signVal)
		{
			$returnMessage = array('code'=> 'error', 'message' => '非法操作');
			echo json_encode($returnMessage);
			exit;
		}
		
		// 验证token
		$uid                  = I('userId', 0);
		$whereUser['u_id']    = $uid;
		$whereUser['u_state'] = 1;
		$token = M('users')->where($whereUser)->getField('token');
		$s_id  = 1;

		
		// 操作数据
		$data['c_name']       = $c_name;
		$data['c_sex']        = $c_sex;
		$data['c_telphone']   = $c_telphone;
		$data['c_idcard']     = $c_idcard;
		$data['c_bankname']   = $c_bankname;
		$data['c_bankcard']   = $c_bankcard;
		$data['c_address']    = $c_address;
		$data['c_called']     = $c_called;
		$data['c_age']        = $c_age;
		$data['c_birthday']   = $c_birthday;
		$data['c_source']     = $c_source;
		$data['c_desc']       = $c_desc;
		$data['c_gettime']    = $c_gettime;
		$data['s_id']         = $s_id;
		$data['create_u_id']  = I('userId', 0);
		$data['u_id']         = $uid;
		$data['create_time']  = date("Y-m-d H:i:s", time());
		
		$saveWhere['c_id'] = $c_id;
		$id = M('customs')->where($saveWhere)->save($data);
		if ($id)
		{
			$returnMessage = array('code'=> 'success', 'message' => '修改成功');
			echo json_encode($returnMessage);
			exit;
		}
		else
		{
			$returnMessage = array('code'=> 'fail', 'message' => '修改失败，请重试');
			echo json_encode($returnMessage);
			exit;
		}
	}
}