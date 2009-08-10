<?php
class MypagesController extends AppController {
	var $name    = 'Mypages';
	var $uses    = array();
//	var $helpers = array('Smarty');
	var $layout  = 'mylayout';
	var $view    = 'Smarty.Smarty';
	
	function index()
	{
	  if ( is_writable(TMP.'smarty'.DS.'templates_c') ) {
      $this->set('smarty_compile_dir_is_writable', true) ;
    }

	  if ( is_writable(TMP.'smarty'.DS.'cache') ) {
      $this->set('smarty_cache_dir_is_writable', true) ;
    }
	}
}
?>