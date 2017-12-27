<?php
namespace Home\Controller;
use Think\Controller;


class WelfareController extends Controller {


	//查询福利列表
	public function getwelfare()
		{
			$Wname = I('post.search_wname', '');
		//$u_id   = I('post.userId', '');
		
			if (!empty($wname)) {
				$whereWelfare['w_name'] = array('like', '%'.$Wname.'%');
			}
			$whereWelfare['w_number'] = array('gt',0);
			$welfareList = M('welfare')->where($whereWelfare)->select();
			
			$array = array('dataValue'=>$welfareList);

			echo json_encode($array);		
		}

	}