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

class JCKManViewImport extends JViewLegacy
{
	protected $app;
	protected $form;
	protected $state;
	protected $paths;

	function display($tpl=null)
	{
		$paths = new stdClass();
		$paths->first = '';
		
		$this->app			= JFactory::getApplication();
		$this->form			= $this->get('Form');
		$this->state		= $this->get('State');
		$this->paths		= $paths;

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JCKHelper::error( implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		
		JHTML::_('behavior.tooltip');
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$bar 	= JToolBar::getInstance('toolbar');
		$canDo 	= JCKHelper::getActions();

		JToolBarHelper::title( JText::_( 'Restore Backup' ), 'plugin.png' );

		if( !JCKHelper::isMobile() && !JCKHelper::isiPad() )
		{
			$bar->appendButton( 'Link', 'export', 'Export','index.php?option=com_jckman&task=cpanel.export');
		}

		$bar->appendButton( 'Link', 'cpanel', 'Control Panel', 'index.php?option=com_jckman&view=cpanel');
		JToolBarHelper::help( $this->app->input->get( 'view' ), false, 'http://www.joomlackeditor.com/installation-guide?start=20#restore');

		JCKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function
}