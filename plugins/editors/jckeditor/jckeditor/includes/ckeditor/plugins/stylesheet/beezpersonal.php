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


class plgStylesheetBeezPersonal extends JPlugin 
{
		
  	function plgStylesheetBeezPersonal(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function load(&$params)
	{
		
		$template = $params->get('default_beez_template',false);
				
		if(!$template)
			return false;
		
		$stylesheet = JPATH_SITE.'/templates/'. $template.'/css/personal.css';	
        if(!file_exists($stylesheet))
            return false;  
		$cssContent = file_get_contents($stylesheet);
		return htmlspecialchars_decode($cssContent, ENT_COMPAT);
			
	}

}