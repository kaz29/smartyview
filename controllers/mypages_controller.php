<?php
class MypagesController extends AppController {
	var $name    = 'Mypages';
	var $uses    = array();
//	var $helpers = array('Smarty');
	var $layout  = 'mylayout';
	var $view    = 'Smarty.Smarty';
	
	function index(){
		$this->set('smarty_content', 'Testing SmartyView');
	}
}
?>