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


class plgCoreEnv extends JPlugin 
{
		
  	function plgCoreEnv(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}
	

	//default method that is called
	function intialize(&$params) // Editor's params passed in
	{
		
		
		$javascript = new JCKJavascript();
		$javascript->addScriptDeclaration("
						
			(function()
			{
				var mooTools = typeof MooTools != 'undefined' ? MooTools : false , 
				browser = typeof Browser != 'undefined' ? Browser : false;
				
				CKEDITOR.env.ie9Compat = document.documentMode == 9;
				
				CKEDITOR.env.opera = mooTools && browser && browser.Engine && (browser.Engine.name == 'presto') && true || CKEDITOR.env.opera;
				
				CKEDITOR.env.version = 
					mooTools && CKEDITOR.env.opera && browser && browser.Engine && browser.Engine.version && browser.Engine.version/100 || CKEDITOR.env.version;
				
				var env = CKEDITOR.env;
					version = CKEDITOR.env.version;
					
				var agent = navigator.userAgent.toLowerCase();	
				
				CKEDITOR.env.iOS = /(ipad|iphone|ipod)/.test(agent); 	
				
				CKEDITOR.env.isCompatible =
				
				// White list of mobile devices that supports. Patch 7190
				env.iOS && version >= 534 || 
				
				!env.mobile && (
				( env.ie && version >= 6 ) ||
				( env.gecko && version >= 10801 ) ||
				( env.opera && version >= 9.5 ) ||
				( env.air && version >= 1 ) ||
				( env.webkit && version >= 522 ) ||
				false );	
			}
			)();");
		return $javascript->toRaw();
	}
	
}

		


