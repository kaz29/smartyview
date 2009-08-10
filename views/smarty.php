<?php
/* SVN FILE: $Id: view.php 8283 2009-08-03 20:49:17Z gwoo $ */
/**
 * Methods for displaying presentation data in the view.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view
 * @since         CakePHP(tm) v 0.10.0.1076
 * @version       $Revision: 8283 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2009-08-03 13:49:17 -0700 (Mon, 03 Aug 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Included libraries.
 */
App::import('Vendor', 'Smarty', array('file' => 'smarty'.DS.'Smarty.class.php'));
/**
 * SmartyView, the V in the MVC triad.
 *
 * Class holding methods for displaying presentation data.
 *
 * @copyright		Copyright 2008-2009, ECWorks.
 * @link			http://www.ecworks.jp/ ECWorks
 * @version			1.2.4.8284
 * @package			cake
 * @subpackage		cake.app.views
 * @lastmodified	$Date: 2009-08-10 00:00:00 +0900 (Mon, 10 Aug 2009) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
class SmartyView extends View {

/**
 * Instance of Smarty object.
 *
 * @var object
 */
	var $smarty;

/**
 * Constructor
 *
 * @return SmartyView
 */
	function __construct(&$controller) {

		parent::__construct($controller);

		$this->subDir = 'smarty';
		$this->ext= '.tpl';
		$this->layoutPath = 'smarty';	//ver. 1.2's property
		
		$this->smarty = &new Smarty();
		$this->smarty->plugins_dir[] = VENDORS.'smarty'.DS.'plugins'.DS;
		$this->smarty->compile_dir = TMP.'smarty'.DS.'templates_c'.DS;
		$this->smarty->cache_dir = TMP.'smarty'.DS.'cache'.DS;
		$this->smarty->error_reporting = 'E_ALL & ~E_NOTICE';

    if ( !is_null(Configure::read( 'Smarty.subDir' )) ) {
		  $this->subDir = Configure::read( 'Smarty.subDir' );
		  if ( empty($this->subDir) ) 
		    $this->subDir = null ;
    }
    
    if ( !is_null(Configure::read( 'Smarty.ext' )) ) {
		  $this->ext = Configure::read( 'Smarty.ext' );
    }
    
    if ( !is_null(Configure::read( 'Smarty.layoutPath' )) ) {
		  $this->layoutPath = Configure::read( 'Smarty.layoutPath' );
		  if ( empty($this->layoutPath) ) 
		    $this->layoutPath = null ;
    }	
    
    if ( !is_null(Configure::read( 'Smarty.left_delimiter' )) ) {
		  $this->left_delimiter = Configure::read( 'Smarty.left_delimiter' );
    }
    
    if ( !is_null(Configure::read( 'Smarty.right_delimiter' )) ) {
		  $this->right_delimiter = Configure::read( 'Smarty.right_delimiter' );
    }
	}

/**
 * Renders a layout. Returns output from _render(). Returns false on error.
 *
 * @param string $content_for_layout Content to render in a view, wrapped by the surrounding layout.
 * @return mixed Rendered output, or false on error
 */
	function renderLayout($content_for_layout, $layout = null) {
		$layoutFileName = $this->_getLayoutFileName($layout);
		if (empty($layoutFileName)) {
			return $this->output;
		}

		$debug = '';

		if (isset($this->viewVars['cakeDebug']) && Configure::read() > 2) {
			$params = array('controller' => $this->viewVars['cakeDebug']);
			$debug = View::element('dump', $params, false);
			unset($this->viewVars['cakeDebug']);
		}

		if ($this->pageTitle !== false) {
			$pageTitle = $this->pageTitle;
		} else {
			$pageTitle = Inflector::humanize($this->viewPath);
		}
		$data_for_layout = array_merge($this->viewVars, array(
			'title_for_layout' => $pageTitle,
			'content_for_layout' => $content_for_layout,
			'scripts_for_layout' => join("\n\t", $this->__scripts),
			'cakeDebug' => $debug
		));

		if (empty($this->loaded) && !empty($this->helpers)) {
			$loadHelpers = true;
		} else {
			$loadHelpers = false;
			$data_for_layout = array_merge($data_for_layout, $this->loaded);
		}

		$this->_triggerHelpers('beforeLayout', 'beforeSmartyLayout');

		if (substr($layoutFileName, -3) === 'ctp' || substr($layoutFileName, -5) === 'thtml') {
			$this->output = View::_render($layoutFileName, $data_for_layout, $loadHelpers, true);
		} else {
			$this->output = $this->_render($layoutFileName, $data_for_layout, $loadHelpers, true);
		}

		if ($this->output === false) {
			$this->output = $this->_render($layoutFileName, $data_for_layout);
			$msg = __("Error in layout %s, got: <blockquote>%s</blockquote>", true);
			trigger_error(sprintf($msg, $layoutFileName, $this->output), E_USER_ERROR);
			return false;
		}

		$this->_triggerHelpers('afterLayout', 'afterSmartyLayout');

		return $this->output;
	}
/**
 * Fire a callback on all loaded Helpers
 *
 * @param string $callback name of callback fire.
 * @access protected
 * @return void
 */
	function _triggerHelpers($callback, $callback_smarty) {
		if (empty($this->loaded)) {
			return false;
		}
		$helpers = array_keys($this->loaded);
		foreach ($helpers as $helperName) {
			$helper =& $this->loaded[$helperName];
			if (is_object($helper)) {
				if (is_subclass_of($helper, 'Helper')) {
					$helper->{$callback}();
				}
				if (method_exists($helper, $callback_smarty)) {
					$helper->{$callback_smarty}($this->smarty);
				}
			}
		}
	}
/**
 * Renders and returns output for given view filename with its
 * array of data.
 *
 * @param string $___viewFn Filename of the view
 * @param array $___dataForView Data to include in rendered view
 * @return string Rendered output
 * @access protected
 */
	function _render($___viewFn, $___dataForView, $loadHelpers = true, $cached = false) {
		$loadedHelpers = array();

		if ($this->helpers != false && $loadHelpers === true) {
			$loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);

			foreach (array_keys($loadedHelpers) as $helper) {
				$camelBackedHelper = Inflector::variable($helper);
				${$camelBackedHelper} =& $loadedHelpers[$helper];
				$this->loaded[$camelBackedHelper] =& ${$camelBackedHelper};
				$this->smarty->assign_by_ref($camelBackedHelper, ${$camelBackedHelper});
			}

			$this->_triggerHelpers('beforeRender', 'beforeSmartyRender');
		}

		foreach($___dataForView as $data => $value) {
			if(!is_object($data)) {
				$this->smarty->assign($data, $value);
			}
		}
		$this->smarty->assign_by_ref('view', $this);
		ob_start();

		echo $this->smarty->fetch($___viewFn);

		if ($loadHelpers === true) {
			$this->_triggerHelpers('afterRender', 'afterSmartyRender');
		}

		$out = ob_get_clean();
		$caching = (
			isset($this->loaded['cache']) &&
			(($this->cacheAction != false)) && (Configure::read('Cache.check') === true)
		);

		if ($caching) {
			if (is_a($this->loaded['cache'], 'CacheHelper')) {
				$cache =& $this->loaded['cache'];
				$cache->base = $this->base;
				$cache->here = $this->here;
				$cache->helpers = $this->helpers;
				$cache->action = $this->action;
				$cache->controllerName = $this->name;
				$cache->layout	= $this->layout;
				$cache->cacheAction = $this->cacheAction;
				$cache->cache($___viewFn, $out, $cached);
			}
		}
		return $out;
	}
	
  function aa()
  {
      $args = func_get_args();
      return call_user_func_array('aa', $args);
  }
}
