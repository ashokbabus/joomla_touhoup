<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// no direct access
defined( '_JEXEC' ) or die();

// Require specific controller
// Controller

// -PC- J3.0 fix
if( !defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

//load base classes
require_once (JPATH_COMPONENT.DS.'base'.DS.'loader.php');

//defines CKEDITOR library includes path
define('CKEDITOR_LIBRARY',JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'jckeditor'.DS.'includes'.DS.'ckeditor'); 

define('JCK_COMPONENT', JUri::root() . 'administrator/components/com_jckman');

//load  default style sheets
$document = JFactory::getDocument();
$document->addStyleSheet( JCK_COMPONENT . '/css/header.css', 'text/css' );

jckimport('base.controller');

//register all event listeners
JCKRegisterAllEventlisetners();
$app = JFactory::getApplication();

$controllername = '';

$task = $app->input->get('task','' );

if(strpos($task,'.'))
	list($controllername,$task) = explode('.',$task);

if($controllername)
  $app->input->set('controller',$controllername);

if(is_dir(CKEDITOR_LIBRARY))
{
	require_once('helper.php');
	$view = $app->input->get('view','cpanel' );
}	
else
{	
	require_once('helper.php');
	$view = 'cpanel';
	$app->enqueueMessage("System couldn't detect JoomlaCK Editor: Please check file permissions or ensure you have installed the editor");
}	

// Require specific controller if requested
jckimport('controllers.' . $view );

if($view == "install")
{
	require_once (JPATH_COMPONENT.DS.'controllers'.DS. 'install.php');

	//load language file,
	$lang = JFactory::getLanguage();
	$lang->load('com_installer',JPATH_ADMINISTRATOR);

	// Create the controller
	jimport('joomla.client.helper');
	$controller = JControllerLegacy::getInstance('Installer',array(
	'base_path' =>  dirname( __FILE__ )));

	if(!is_a($controller,'InstallerController'))
	{
		$app->setUserState('com_installer.redirect_url', 'index.php?option=com_jckman&view=install');
	}

	$controller->execute($app->input->get( 'task' ));
	$controller->redirect();
}
else 
{
    // main helper class
	jckimport('helper');
	// global include classes
	jckimport('parameter.parameter');
	jckimport('html.html');

	$controller =  JControllerLegacy::getInstance('JCKMan');
	$controller->execute($app->input->get( 'task' ));
	$controller->redirect();
}