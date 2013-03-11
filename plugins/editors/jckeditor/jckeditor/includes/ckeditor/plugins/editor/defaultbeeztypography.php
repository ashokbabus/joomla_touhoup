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


class plgEditorDefaultBeezTypography extends JPlugin 
{
		
  	function plgEditorDefaultBeezTypography(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function beforeLoad(&$params)
	{
		//lets create JS object
		
		$javascript = new JCKJavascript();
				
		$db = JFactory::getDBO();
		
		$query	= $db->getQuery(true);

		$query->select('template');
		$query->from('#__template_styles');
		$query->where('client_id=0 AND home=1');

		$db->setQuery( $query );
		$template = $db->loadResult();
		
		$base  = str_replace("administrator","",JURI::base()).'templates/'.$template .'/css/';
		
		$templates =  array(
			//$base.'position.css',
			$base.'layout.css',
			$base.'personal.css'							
		);	
	
		$javascript->addScriptDeclaration(
			"editor.on( 'configLoaded', function()
			{
				if(editor.config.contentsCss instanceof Array)
					editor.config.contentsCss.push('".implode("','",$templates)."');
				else 
					editor.config.contentsCss = [editor.config.contentsCss,'".implode("','",$templates)."'];
			});"	
		);
		
		if($params->get('styles_css',true))
		{
			$javascript->addScriptDeclaration(
				"editor.on( 'configLoaded', function()
				{
					if(editor.config.stylesCss instanceof Array)
						editor.config.stylesCss.push('".implode("','",$templates)."');
					else if(editor.config.stylesCss) 
						editor.config.stylesCss = [editor.config.stylesCss,'".implode("','",$templates)."'];
					else
						editor.config.stylesCss = ['".implode("','",$templates)."'];
				});"	
			);
		
		}
		
		return $javascript->toRaw();
		
	}

}