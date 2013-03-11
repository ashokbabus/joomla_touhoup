<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

include('../../../includes.php');
jimport('joomla.filesystem.file');

defined( '_JEXEC' ) or die( 'Restricted access' );


//Needed for 1.6
define('JDEBUG',false);

if(!jckimport('ckeditor.authenticate'))
	die(false);
	


abstract class JTreeLinkHelper
{
	public static function ListExtensions(& $extensions)
	{
	
		if(!is_dir(JPATH_ADMINISTRATOR.'/components/com_jckman'))
			return;
		
		$root =  JURI::root().'../../';	
		$base =  JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'plugins';
		
		
		$db = JFactory::getDBO();
		
		$query = '	SELECT ext.name FROM #__jckplugins ext
					INNER JOIN #__jckplugins parent on parent.id = ext.parentid
					WHERE parent.name = "jtreelink"
					AND parent.published = 1
					AND  ext.published = 1';
					
		$db->setQuery($query);
		
		$results = $db->loadColumn();
		
		if(empty($results))
			return;
	
		
		
		foreach($results as $extension)
		{
			$path = $base.DS.$extension.DS.'images'.DS.'icon.gif';
			$url = $root.$extension.'/images/icon.gif';	
			
			$icon = array('_open','_closed'); //We default to default icon if no custom icon has been supplied by plugin.
			
			if(JFile::exists($path))
			{
				$icon = array($url,$url);
			}
			else
			{	
				$path = $base.DS.$extension.DS.'images'.DS.'icon.png';
				$url = $root.$extension.'/images/icon.png';	
				
				if(JFile::exists($path))
					$icon = array($url,$url);
			}
			
			$extensions[$extension] =  $icon;

		}	

	}

}

//now lets echo responese
header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8" ?>',"\n";

echo "<nodes>\n";

$config = JFactory::getConfig();
$config->set('live_site','');

// Inialize array
$root = JURI::root();

$contentIcon =  $root.'../../'.'jtreelink/images/icon.png';	
$menuIcon = $root.'../../'.'jtreelink/images/menu.png';	
$extensions = array('content'=>array($contentIcon,$contentIcon) ,'menu'=>array($menuIcon,$menuIcon));

JTreeLinkHelper::ListExtensions($extensions);

$client = JRequest::getInt('client',0);

foreach($extensions as $extension=>$icon)
{
	$load = $root .'links.php?extension='.$extension .'&amp;client='.$client;
	echo '<node text="' . ucfirst($extension).'" openicon="'.$icon[0].'" icon="'.$icon[1].'" load="'. $load . '"  selectable="false" url ="">' . "\n";
	echo "</node>\n";
}

echo "</nodes>";

?>

