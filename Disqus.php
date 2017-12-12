<?php

if (function_exists('wfLoadExtension')) {
	wfLoadExtension('Disqus');
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['Disqus'] = __DIR__ . '/i18n';
	return true;
}
else {
	die('This version of the Disqus extension requires MediaWiki 1.25+');
}