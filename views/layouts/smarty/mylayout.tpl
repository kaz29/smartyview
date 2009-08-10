<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	{$html->charset()}
	<title>
		SmartyView plugin:
		{$title_for_layout}
	</title>
	{$html->meta('icon')}
  {$html->css('cake.generic')}
  {$scripts_for_layout}
</head>
<body>
	<div id="container">
		<div id="header">
			<h1>{$html->link('SmartyView plugin', 'http://blog.ecworks.jp/smartyview')}</h1>
		</div>
		<div id="content">
			{$session->flash()}
			{$content_for_layout}

		</div>
		<div id="footer">
			{$html->link($html->image('cake.power.gif'),'http://www.cakephp.org/', $view->aa('target','_blank'), null, false)}
		</div>
	</div>
	{$cakeDebug}
</body>
</html>