<?php
class SmartyHelper extends Helper {

	function beforeSmartyRender(&$smarty){
		
		echo "beforeSmartyRender<br>\n";
		
		//$smarty->left_delimiter = '{{';
		//$smarty->right_delimiter = '}}';
	}
	
	function beforeSmartyLayout(&$smarty){
		
		echo "beforeSmartyLayout<br>\n";
		
	}
	
	function afterSmartyLayout(&$smarty){
		
		echo "afterSmartyLayout<br>\n";
		
	}
	
	function afterSmartyRender(&$smarty){
		
		echo "afterSmartyRender<br>\n";
		
	}
	
}
