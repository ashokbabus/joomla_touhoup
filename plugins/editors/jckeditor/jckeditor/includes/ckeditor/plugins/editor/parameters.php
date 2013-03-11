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


class plgEditorParameters extends JPlugin 
{
		
  	function plgEditorParameters(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function beforeLoad(&$params)
	{
		//lets create JS object	
		$javascript = new JCKJavascript();
		
		$script = "editor.on( 'configLoaded', function()
		{
			if(editor.config.extraPlugins)
				editor.config.extraPlugins += ',paneloverride';
			else
				editor.config.extraPlugins += 'paneloverride';
		});";
	
		$javascript->addScriptDeclaration($script);
		return $javascript->toRaw();
	}
	
	function afterLoad(&$params)
	{
		
		//lets create JS object
		$javascript = new JCKJavascript();
		
		$imagePath = $params->get('imagePath','images');
		$imagePath = preg_replace('/(^\/|\/$)/','',$imagePath);
		
		$ftfamily = $params->get('ftfamily','');
		$ftsize = $params->get('ftsize','');
		$bgcolor	= 	$params->get( 'bgcolor','#ffffff');
		$ftcolor	= 	$params->get( 'ftcolor','');
		
				
		$javascript->addScriptDeclaration("
			editor.config['imagePath'] = '" . $imagePath . "';".
			($ftfamily ? "editor.config['ftfamily'] = '" . $ftfamily . "';" : "").
			($ftsize ? "editor.config['ftsize'] = '" . $ftsize . "';" :"")."
			editor.config['bgcolor'] = '" . $bgcolor . "';".
			($ftcolor ? "editor.config['ftcolor'] = '" . $ftcolor . "';" :""));
		return $javascript->toRaw();
		
	}

}