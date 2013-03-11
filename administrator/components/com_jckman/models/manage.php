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

// Import library dependencies
require_once dirname(__FILE__) . '/extension.php';

class JCKManModelManage extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Remove (uninstall) an extension
	 *
	 * @param	array	An array of identifiers
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function remove($eid = array())
	{
		// Initialise variables.
		$user = JFactory::getUser();

		if($user->authorise('core.delete', 'com_jckman'))
		{
			// Initialise variables.
			$failed 	= array();
			$db 		= JFactory::getDBO();
			$app		= JFactory::getApplication();
			$lang		= JFactory::getLanguage();

			/*
			* Ensure eid is an array of extension ids in the form id => client_id
			* TODO: If it isn't an array do we want to set an error and fail?
			*/
			if (!is_array($eid)) {
				$eid = array($eid);
			}

			// Get an installer object for the extension type
			require_once( JPATH_COMPONENT .DS.'helpers'.DS.'installer.php' );
			$view 		= $app->input->get('view',false);
			$installer 	=& JCKInstaller::getInstance();

			// Uninstall the chosen extensions	
			foreach($eid as $id) {
				$id = trim($id);
				if ($view) {
					$result = $installer->uninstall($view, $id);

					// Build an array of extensions that failed to uninstall
					if ($result === false) {
						$failed[] = $id;
					}
				}
				else {
					$failed[] = $id;
				}
			}

			$lang->load( 'com_installer' );
			$langstring = 'COM_INSTALLER_TYPE_TYPE_'. strtoupper($row->type);
			$rowtype 	= JText::_($langstring);
			if(strpos($rowtype, $langstring) !== false) {
				$rowtype = $row->type;
			}

			if (count($failed)) {

				// There was an error in uninstalling the package
				$msg = JText::sprintf('COM_INSTALLER_UNINSTALL_ERROR', $rowtype);
				$result = false;
			} else {

				// Package uninstalled sucessfully
				$msg = JText::sprintf('COM_INSTALLER_UNINSTALL_SUCCESS', $rowtype);
				$result = true;
			}
			$app->enqueueMessage($msg);
			$this->setState('action', 'remove');
			$this->setState('name', $installer->get('name'));
			$app->setUserState('com_jckman.message', $installer->message);
			$app->setUserState('com_jckman.extension_message', $installer->get('extension_message'));
			return $result;
		} else {
			$result = false;
			JCKHelper::error( JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
		}
	}
}