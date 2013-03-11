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


class plgEditorSigParameters extends JPlugin 
{
		
  	function plgEditorSigParameters(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function afterLoad(&$params)
	{
		
		//lets create JS object
		$javascript = new JCKJavascript();
		
		$plugin = JPluginHelper::getPlugin('content','jw_sigpro');
		
		if(empty($plugin))
			$plugin = JPluginHelper::getPlugin('content','jwsig');
			
		if(empty($plugin))
			$plugin = JPluginHelper::getPlugin('content','jw_simpleImageGallery');	
		
		if(empty($plugin) && !isset($plugin->params))
			return;
					
		
		$sigParams =  new JRegistry($plugin->params);
		
		$sigPath =  $sigParams->get('galleries_rootfolder','images/stories');
		
		$sigPath  = preg_replace('/(^\/|\/$)/','',$sigPath);	
		
		$sigPath = preg_replace('/(^\/|\/$)/','',$sigPath);
		
		$javascript->addScriptDeclaration("
			editor.config['sigPath'] = '" . $sigPath . "';");
		
		return $javascript->toRaw();
		
	}

}