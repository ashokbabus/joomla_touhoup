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

//jimport( 'joomla.installer.installer' );
jimport('joomla.installer.helper');

class JCKManModelImport extends JModelLegacy
{
	/** @var object JTable object */
	var $_table = null;

	/** @var object JTable object */
	var $_url = null;

	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		parent::__construct();

	}

	function getForm( $data = array(), $loadData = true )
	{
		// J3.0 workaround
	}

	function import()
	{
		$mainframe =& JFactory::getApplication();

		$this->setState('action', 'install');

		switch(JRequest::getWord('installtype'))
		{
			case 'folder':
				$package = $this->_getPackageFromFolder();
				break;

			case 'upload':
				$package = $this->_getPackageFromUpload();
				break;


			default:
				$this->setState('message', 'No Install Type Found');
				return false;
				break;
		}

		// Was the package unpacked?
		if (!$package) {
			$this->setState('message', 'Unable to find install package');
			return false;
		}

		// Get a database connector
		//$db = & JFactory::getDBO();

		// Get an installer instance
		require_once( JPATH_COMPONENT .DS. 'helpers' .DS.'restorer.php' );
		$installer =& JCKRestorer::getInstance();

		// Install the package
		if (!$installer->install($package['dir'])) {
			// There was an error installing the package
			$msg = JText::sprintf('COM_INSTALLER_INSTALL_ERROR', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
			$result = false;
		} else {
			// Package installed sucessfully
			$msg = JText::sprintf('COM_INSTALLER_INSTALL_SUCCESS', JText::_('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
			$result = true;
		}

		// Set some model state values
		$mainframe->enqueueMessage($msg);
		$this->setState('name', $installer->get('name'));
		$this->setState('result', $result);
		$this->setState('message', $installer->message);
		$this->setState('extension.message', $installer->get('extension.message'));

		// Cleanup the install files
		if (!is_file($package['packagefile'])) {
			$config =& JFactory::getConfig();
			$package['packagefile'] = $config->get('config.tmp_path').DS.$package['packagefile'];
		}

		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		return $result;
	}

		/**
	 * Works out an installation package from a HTTP upload
	 *
	 * @return package definition or false on failure
	 */
	protected function _getPackageFromUpload()
	{
		// Get the uploaded file information
		$userfile = JRequest::getVar('install_package', null, 'files', 'array');

		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads')) {
			JCKHelper::error( JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
			return false;
		}

		// Make sure that zlib is loaded so that the package can be unpacked
		if (!extension_loaded('zlib')) {
			JCKHelper::error( JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
			return false;
		}

		// If there is no uploaded file, we have a problem...
		if (!is_array($userfile)) {
			JCKHelper::error( JText::_('COM_INSTALLER_MSG_INSTALL_NO_FILE_SELECTED'));
			return false;
		}

		// Check if there was a problem uploading the file.
		if ($userfile['error'] || $userfile['size'] < 1) {
			JCKHelper::error( JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
			return false;
		}

		// Build the appropriate paths
		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path').DS.$userfile['name'];
		$tmp_src	= $userfile['tmp_name'];

		// Move uploaded file
		jimport('joomla.filesystem.file');
		$uploaded = JFile::upload($tmp_src, $tmp_dest);

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest);

		return $package;
	}

	/**
	 * Install an extension from a directory
	 *
	 * @static
	 * @return boolean True on success
	 * @since 1.0
	 */
	protected function _getPackageFromFolder()
	{
		// Get the path to the package to install
		$p_dir = JRequest::getString('install_directory');
		$p_dir = JPath::clean($p_dir);

		// Did you give us a valid directory?
		if (!is_dir($p_dir)) {
			JCKHelper::error( JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_ENTER_A_PACKAGE_DIRECTORY'));
			return false;
		}

		// Detect the package type
		$type = JInstallerHelper::detectType($p_dir);

		// Did you give us a valid package?
		if (!$type) {
			JCKHelper::error( JText::_('COM_INSTALLER_MSG_INSTALL_PATH_DOES_NOT_HAVE_A_VALID_PACKAGE'));
			return false;
		}

		$package['packagefile'] = null;
		$package['extractdir'] = null;
		$package['dir'] = $p_dir;
		$package['type'] = $type;

		return $package;
	}
	
}