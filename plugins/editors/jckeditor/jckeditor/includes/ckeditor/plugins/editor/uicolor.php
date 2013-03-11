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


class plgEditorUIColor extends JPlugin 
{
		
  	function plgEditorUIColor(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function beforeLoad(&$params)
	{
		//lets create JS object
		$javascript = new JCKJavascript();
		
		$defaultColor = $params->get('uicolor','#D6E6F4');
		
		$user = JFactory::getUser();
		
		$color = $user->getParam('jckuicolor',$defaultColor);
			
		if($color == $defaultColor) //already set so just exit
			return;
		
		$javascript->addScriptDeclaration(
			"editor.on( 'configLoaded', function()
			{
				editor.config.uiColor = '".$color."';
			});"	
		);
		
		return $javascript->toRaw();
		
	}

}