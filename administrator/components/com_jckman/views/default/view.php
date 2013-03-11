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

class InstallerViewDefault extends JViewLegacy
{
	function __construct($config = null)
	{
		parent::__construct(array(
			'base_path' =>  JPATH_COMPONENT
		));
		
		$this->_addPath('template', $this->_basePath.DS.'views'.DS.'default'.DS.'tmpl');
	}

	function display($tpl=null)
	{
		/*
		 * Set toolbar items for the page
		 */
		JToolBarHelper::title( JText::_( 'JCK Plugin Installer'), 'installer.png' );


		// Document
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('JCK Plugin Installer').' : '.JText::_( $this->getName() ));

		// Get data from the model
		$state		= $this->get('State');

		// Are there messages to display ?
		$showMessage	= false;
		if ( is_object($state) )
		{
			$message1		= $state->get('message');
			$message2		= $state->get('extension.message');
			$showMessage	= ( $message1 || $message2 );
		}

		$this->assign('showMessage',	$showMessage);
		$this->assignRef('state',		$state);

		JHTML::_('behavior.tooltip');
		parent::display($tpl);
	}

	/**
	 * Should be overloaded by extending view
	 *
	 * @param	int $index
	 */
	function loadItem($index=0)
	{
	}
}