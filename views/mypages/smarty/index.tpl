<p>
{if $smarty_compile_dir_is_writable}
<span class="notice success">Your smarty compile directory is writable.</span>
{else}
<span class="notice">Your smarty compile directory is NOT writable.</span>
{/if}
</p>

<p>
{if $smarty_cache_dir_is_writable}
<span class="notice success">Your smarty cache directory is writable.</span>
{else}
<span class="notice">Your smarty cache directory is NOT writable.</span>
{/if}
</p>

<h3>Getting Started</h3>
<p>
	<a href="http://blog.ecworks.jp/smartyview/">SmartyView official page.</a>
</p>
<div>
<p>
  Copyright 2008-2009, {$html->link('ECWorks', 'http://www.ecworks.jp/')}.
</p>
<p>
  {$html->link('kaz_29', 'http://d.hatena.ne.jp/kaz_29/', $view->aa('target','_blank'))}
</p>
</div>