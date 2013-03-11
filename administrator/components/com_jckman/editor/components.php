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


class plgToolbarComponents extends JPlugin 
{
		
  	function plgToolbarComponents(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function load(&$params)
	{
	
		
	
		$defaults = array(strtolower($params->get('toolbar','full')),strtolower($params->get('toolbar_ft','full')) );

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('name,params')
				->from('#__jcktoolbars')
				->where('published = 1')
				->where('LOWER(name) NOT IN("'. implode('","',$defaults).'")')
				->order('id DESC');
		$db->setQuery($query);
		$toolbars = $db->loadObjectList();
		
		if(empty($toolbars))
			return;
			
		$component = JFactory::getApplication()->input->get('option','');
			
		foreach($toolbars as $toolbar)
		{
				$tparams = new JRegistry($toolbar->params);
				$components = $tparams->get('components',array(0));
				
				if(in_array($component,$components,true))
				{
					$name = ucfirst($toolbar->name);
					$params->set('toolbar',$name);
					$params->set('toolbar_ft',$name);
					break;
				}

		}		
		return;
	}

}