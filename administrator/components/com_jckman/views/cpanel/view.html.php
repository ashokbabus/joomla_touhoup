<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// no direct access
defined( '_JEXEC' ) or die();

jimport('joomla.html.sliders');
jimport('joomla.application.module.helper');

class JCKManViewCpanel extends JViewLegacy
{
	protected $canDo;
	protected $app;
	protected $icons;
	protected $modules;
	
	function display( $tpl = null )
	{
		$this->canDo	= JCKHelper::getActions();
		$this->app		= JFactory::getApplication();
		$lang 			= JFactory::getLanguage();		
		$this->icons	= JCKModuleHelper::getModules('jck_icon');
		$this->modules	= JCKModuleHelper::getModules('jck_cpanel');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$bar 	= JToolBar::getInstance('toolbar');

		JToolBarHelper::title( JText::_( 'JCK Manager' ), 'cpanel.png' );

		if ($this->canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_jckman');
		}
		
		JCKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function
}