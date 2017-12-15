<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $arry = $_POST;
		echo json_encode($arry);
	}
	
	
	public function returnResponse() {
		$arry = $_POST;
		echo json_encode($arry);
	}
}