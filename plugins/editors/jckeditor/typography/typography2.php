<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

$_REQUEST['eSess'] = 0;

require('../includes.php');

defined( '_JEXEC' ) or die( 'Restricted access' );


//Get editor params

$db = JFactory::getDBO();

$query = $db->getQuery(true);

$query->select('params')
	->from('#__extensions')
	->where('folder = "editors"')
	->where('element ="jckeditor"');

$db->setQuery($query);	
$results = $db->loadResult();

$params =  @ new JRegistry($results);

//import plugins 

$contentCSS = $params->get('jcktypographycontent','');

// Remove comments
$contentCSS = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contentCSS);

// Remove space after colons
$contentCSS = str_replace(': ', ':', $contentCSS);

// Remove whitespace
$contentCSS = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $contentCSS);

// Enable GZip encoding.

if(JFactory::getConfig()->get('gzip',false))
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