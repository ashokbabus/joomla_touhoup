<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2010 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

//wrapper class for Juser
class JCKUser extends JObject
{
	
	static function &getInstance()
    {
				
		static $instance;
		
		if($instance)
			return $instance;
		
		$session = JCKUser::getSession(); //get Session incase it has not been set		
		$instance = $session->get('user'); 
			
		return $instance;
	}
		
	
	static function getSession()
	{
		static $instance;
		if($instance)
			return $instance;
		jckimport('ckeditor.session.session');
		$instance = JCKSession::getSessionInstance();
		return $instance;
	}
	

}