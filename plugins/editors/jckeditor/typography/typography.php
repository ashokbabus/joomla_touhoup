<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

require('../includes.php');

defined( '_JEXEC' ) or die( 'Restricted access' );


$app = JFactory::getApplication('site');

//Get editor params
$plugin = JPluginHelper::getPlugin('editors','jckeditor');

if(is_string($plugin->params)) //always must do this check
$params = @ new JRegistry($plugin->params);
else $params = 	$plugin->params;

jckimport('ckeditor.plugins.helper');

//import plugins 
JCKPluginsHelper::storePlugins('stylesheet');
JCKPluginsHelper::importPlugin('stylesheet');

$dispatcher =  JDispatcher::getInstance();

$results  =  $dispatcher->trigger('load',array(&$params));

$contentCSS = implode("",$results); 

// Remove comments
$contentCSS = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contentCSS);

// Remove space after colons
$contentCSS = str_replace(': ', ':', $contentCSS);

// Remove whitespace
$contentCSS = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $contentCSS);

// Enable GZip encoding.
if($app->getCfg('gzip', false))
{
	if(!ini_get('zlib.output_compression') && ini_get('output_handler')!='ob_gzhandler') //if server is configured to do this then leave it the server to do it's stuff
	ob_start("ob_gzhandler");
}
// Enable caching
header('Cache-Control: public'); 

// Expire in one day
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT'); 

// Set the correct MIME type, because Apache won't set it for us
header("Content-type: text/css");

// Write everything out
echo $contentCSS;
?>



