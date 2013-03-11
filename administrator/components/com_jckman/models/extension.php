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

define( 'JCK_PATH', JPATH_PLUGINS.DS.'editors'.DS.'jckeditor' );
define( 'JCK_PLUGINS', JCK_PATH.DS.'plugins' );

class JCKManModelInstaller extends JModelList
{
	/** @var array Array of installed components */
	var $_items = array();

	/** @var object JPagination object */
	var $_pagination = null;

	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		$mainframe = JFactory::getApplication();	

		// Call the parent constructor
		parent::__construct();

		// Set state variables from the request
		$this->setState('pagination.limit',	$mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int'));
		$this->setState('pagination.offset',$mainframe->getUserStateFromRequest('com_installer.limitstart.'.$this->_type, 'limitstart', 0, 'int'));
		$this->setState('pagination.total',	0);
	}

	function &getItems()
	{
		if (empty($this->_items)) {
			// Load the items
			$this->_loadItems();
		}
		return $this->_items;
	}

	function &getPagination()
	{
		if (empty($this->_pagination)) {
			// Make sure items are loaded for a proper total
			if (empty($this->_items)) {
				// Load the items
				$this->_loadItems();
			}
			// Load the pagination object
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->state->get('pagination.total'), $this->state->get('pagination.offset'), $this->state->get('pagination.limit'));
		}
		return $this->_pagination;
	}

	/**
	 * Remove (uninstall) an extension
	 *
	 * @static
	 * @param	array	An array of identifiers
	 * @return	boolean	True on success
	 * @since 1.0
	 */
	function remove($eid=array())
	{
		$mainframe =& JFactory::getApplication();	

		// Initialize variables
		$failed = array ();

		/*
		 * Ensure eid is an array of extension ids in the form id => client_id
		 * TODO: If it isn't an array do we want to set an error and fail?
		 */
		if (!is_array($eid)) {
			$eid = array($eid => 0);
		}

		// Get a database connector
		$db = JFactory::getDBO();

		// Get an installer object for the extension type
		//jimport('joomla.installer.installer');
		//$installer = & JInstaller::getInstance();
		require_once( JPATH_COMPONENT .DS. 'installer.php' );
		
		$installer =  JCKInstaller::getInstance();

		// Uninstall the chosen extensions
		foreach ($eid as $id => $clientId)
		{
			$id		= trim( $id );
			$result	= $installer->uninstall($this->_type, $id, $clientId );

			// Build an array of extensions that failed to uninstall
			if ($result === false) {
				$failed[] = $id;
			}
		}

		if (count($failed)) {
			// There was an error in uninstalling the package
			$msg = JText::sprintf('UNINSTALLEXT', JText::_($this->_type), JText::_('Error'));
			$result = false;
		} else {
			// Package uninstalled sucessfully
			$msg = JText::sprintf('UNINSTALLEXT', JText::_($this->_type), JText::_('Success'));
			$result = true;
		}

		$mainframe->enqueueMessage($msg);
		$this->setState('action', 'remove');
		$this->setState('name', $installer->get('name'));
		$this->setState('message', $installer->message);
		$this->setState('extension.message', $installer->get('extension.message'));

		return $result;
	}

	function _loadItems()
	{
		return JCKHelper::error( JText::_('Method Not Implemented'));
	}
}