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


class plgEditorFsaParameters extends JPlugin 
{
		
  	function plgEditorFsaParameters(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function beforeLoad(&$params)
	{
		
		//lets create JS object
		$javascript = new JCKJavascript();
		
		
		
		$forcesimpleAmpersand = $params->get('forcesimpleAmpersand',0);
		
		$javascript->addScriptDeclaration("
			editor.config['forcesimpleAmpersand'] = " . (int) $forcesimpleAmpersand . ";");
		return $javascript->toRaw();
		
	}

}