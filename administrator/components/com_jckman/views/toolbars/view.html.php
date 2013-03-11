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

class JCKManViewToolbars extends JViewLegacy
{
	protected $canDo;
	protected $app;
	protected $user;
	protected $state;
	protected $items;
	protected $pagination;

	function display( $tpl = null )
	{
		$this->canDo		= JCKHelper::getActions();
		$this->app			= JFactory::getApplication();
		$this->user			= JFactory::getUser();
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JCKHelper::error( implode("\n", $errors));
			return false;
		}

		// Check if there are no matching items
		if(!count($this->items))
		{
			JCKHelper::error( 'No Toolbars Found.' );
		}
		
		//now lets get default toolbars
		$editor = JPluginHelper::getPlugin('editors','jckeditor');
		$params =  new JRegistry($editor->params);
		$this->default = $params->get('toolbar','Full'); 
		$this->defaultFT = $params->get('toolbar_ft','Full'); 
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$bar 	= JToolBar::getInstance('toolbar');

		JToolBarHelper::title( JText::_( 'Layout Manager' ), 'layout.png' );

		if($this->canDo->get('core.create'))
		{
			JToolBarHelper::addNew( 'toolbars.add' );
		}

		if($this->canDo->get('core.edit'))
		{
			JToolBarHelper::editList( 'toolbars.edit' );
		}

		if($this->canDo->get('core.create'))
		{
			JToolBarHelper::custom( 'toolbars.copy', 'copy', 'copy', 'Copy', true );
		}

		if($this->canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList( '', 'toolbars.remove' );
		}

		if($this->canDo->get('core.edit.state'))
		{
			JToolbarHelper::checkin('toolbars.checkin');
		}

		// Add a Link button for Control Panel
		$bar->appendButton( 'Link', 'cpanel', 'Control Panel', 'index.php?option=com_jckman&view=cpanel');
		JToolBarHelper::help( $this->app->input->get( 'view' ), false,'http://www.joomlackeditor.com/installation-guide?start=17#layout_man' );
	
		
		JHtmlSidebar::setAction('index.php?option=com_jckman&view=' . JFactory::getApplication()->input->get( 'view', 'toolbars' ) );

		JCKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			't.title' => JText::_('JGLOBAL_TITLE'),
			't.name' => 'Name',
			't.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}