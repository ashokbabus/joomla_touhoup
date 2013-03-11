<?php 
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.helper'); //keep this in for backward compatiblity
jimport('joomla.filesystem.folder');
jimport('joomla.registry.registry');
jimport('joomla.application.component.helper');
jimport('joomla.log.log');
jimport('joomla.utilities.utility');
require_once(JPATH_SITE.DS.'libraries'.DS.'joomla'.DS.'session'.DS.'storage.php');

class JCKSession
{
	static function  getSessionInstance()
	{
		static $instance;
		
		if($instance)
		{
			return $instance;
		}	
	   
	    $session_name = JCKSession::getName();
	    //session_name( $session_name );

    	$instance =  JFactory::getSession(array('name'=>$session_name));
		
		if(method_exists('JSession','start') && class_exists('JEventDispatcher'))
		{
				if(!$instance->isActive())
				{
					$dispatcher = JEventDispatcher::getInstance();
					$instance->initialise(new JInput, $dispatcher);
					$instance->start();
				}	
		}
		return 	$instance;
	}
    
    
    static function getName()
    {
      
	    $clientId =  JRequest::getInt('client',0,'get');
		
		$client = ($clientId ? 'administrator' : 'site' );
 
		$hash = '';
 
		if(method_exists('JUtility','getHash'))
		    $hash = JUtility::getHash($client);
		else
			 $hash = JApplication::getHash($client);	
			 
	  	return $hash;
    }

}