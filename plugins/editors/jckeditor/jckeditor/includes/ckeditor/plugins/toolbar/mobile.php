<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');
jckimport('ckeditor.htmlwriter.javascript');


class plgToolbarMobile extends JPlugin 
{
		
  	function plgToolbarMobile(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function load(&$params)
	{
		$agent = $_SERVER['HTTP_USER_AGENT'];
		
		if(preg_match('/ipad/i',$agent)) //if iPad  keep full toolbar
			return;
		
		if(preg_match('/(mobi|iphone|ipod|android)/i',$agent) )
		{
			$params->set('toolbar','Mobile');
			$params->set('toolbar_ft','Mobile');
			define('JCK_MOBILE',true);			
		}	
		return;
	}

}