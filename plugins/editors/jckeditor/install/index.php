<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

error_reporting(E_ERROR);

define( '_JEXEC', 1 );

define( 'DS', DIRECTORY_SEPARATOR );
//get root folder
$dir = explode(DS,dirname(__FILE__));

array_splice($dir,-4);

$base_folder = implode(DS,$dir);

define('JPATH_BASE', $base_folder);



require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );


jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

$mainframe = JFactory::getApplication('administrator');

jimport('joomla.plugin.helper');
$plugins = JPluginHelper::getPlugin('system');
 //wipe them out
 foreach($plugins as $plugin)
 {
	$plugin->type = 'notToBeUsed'; //change type from system
 }
$mainframe->initialise();
$mainframe->route();

/**
 * DISPATCH THE APPLICATION
 *
 * NOTE :
 */
$component_path = dirname(__FILE__);
require_once($component_path .DS.'controller.php');
if(class_exists('JControllerLegacy'))
	$controller = JControllerLegacy::getInstance('Install',array('base_path'=>$component_path));
else
	$controller = JController::getInstance('Install',array('base_path'=>$component_path));

 

$task = JRequest::getCmd('task','');
ob_start();
$controller->execute($task);
$controller->redirect();
$contents = ob_get_contents();
ob_end_clean();

ob_start();
$document = JFactory::getDocument();
$head = $document->getBuffer('head');


$head = '<html><head>' . $head. '</head>';
echo $head. '<body>'.$contents .'</body></html>';