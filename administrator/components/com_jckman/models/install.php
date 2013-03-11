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

jimport('joomla.installer.helper');

class InstallerModelInstall extends JModelForm
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
		$form = $this->loadForm('com_jckman.install', JPATH_COMPONENT_ADMINISTRATOR . '/models/form/install.xml', array('control' => 'jform', 'load_data' => $loadData));

		return ( empty( $form ) ) ? false : $form;
	}	
	
	function cancel()
	{
		$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&client='. $client, false ) );
	}

	function install()
	{
		$app = JFactory::getApplication();

		$this->setState('action', 'install');

		switch($app->input->get('installtype'))
		{
			case 'folder':
				$package = $this->_getPackageFromFolder();
				break;

			case 'upload':
				$package = $this->_getPackageFromUpload();
				break;

			case 'url':
				$package = $this->_getPackageFromUrl();
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
		require_once( JPATH_COMPONENT .DS. 'helpers' .DS.'installer.php' );
		$installer =& JCKInstaller::getInstance();

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
		$app->enqueueMessage($msg);
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
		$package = self::unpack($tmp_dest);

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
		$package['extractdir'] 	= null;
		$package['dir'] 		= $p_dir;
		$package['type'] 		= $type;

		return $package;
	}
	/**
	 * Install an extension from a URL
	 *
	 * @return	Package details or false on failure
	 * @since	1.5
	 */
	protected function _getPackageFromUrl()
	{
		// Get a database connector
		$db = JFactory::getDbo();

		// Get the URL of the package to install
		$url = JRequest::getString('install_url');

		// Did you give us a URL?
		if (!$url) {
			JCKHelper::error( JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL'));
			return false;
		}

		// Download the package at the URL given
		$p_file = JInstallerHelper::downloadPackage($url);

		// Was the package downloaded?
		if (!$p_file) {
			JCKHelper::error( JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
			return false;
		}

		$config		= JFactory::getConfig();
		$tmp_dest	= $config->get('tmp_path');

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest.DS.$p_file);

		return $package;
	}
	
	function &getToolbarList()
	{
		$rows = array();
		jckimport('helper');
		$toolbars =& JCKHelper::getEditorToolbars();

		if(!empty($toolbars))
		{
			foreach($toolbars as $toolbar)
			{
				$row = new stdclass;
				$row->text = $toolbar; 
				$row->value = $toolbar;
				$rows[] = $row;
			}
		}

		return $rows;
	}

	/**
	 * Unpacks a file and verifies it as a Joomla element package
	 * Supports .gz .tar .tar.gz and .zip
	 *
	 * @param   string  $p_filename  The uploaded package filename or install directory
	 *
	 * @return  mixed  Array on success or boolean false on failure
	 *
	 * @since   11.1
	 */
	public static function unpack($p_filename)
	{
		// Path to the archive
		$archivename = $p_filename;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');

		// Clean the paths to use for archive extraction
		$extractdir = JPath::clean(dirname($p_filename) . '/' . $tmpdir);
		$archivename = JPath::clean($archivename);

		// Do the unpacking of the archive
		try
		{
			JArchive::extract($archivename, $extractdir);
		}
		catch (Exception $e)
		{
			return false;
		}

		/*
		 * Let's set the extraction directory and package file in the result array so we can
		 * cleanup everything properly later on.
		 */
		$retval['extractdir'] = $extractdir;
		$retval['packagefile'] = $archivename;

		/*
		 * Try to find the correct install directory.  In case the package is inside a
		 * subdirectory detect this and set the install directory to the correct path.
		 *
		 * List all the items in the installation directory.  If there is only one, and
		 * it is a folder, then we will set that folder to be the installation folder.
		 */
		$dirList = array_merge(JFolder::files($extractdir, ''), JFolder::folders($extractdir, ''));

		if (count($dirList) == 1)
		{
			if (JFolder::exists($extractdir . '/' . $dirList[0]))
			{
				$extractdir = JPath::clean($extractdir . '/' . $dirList[0]);
			}
		}

		/*
		 * We have found the install directory so lets set it and then move on
		 * to detecting the extension type.
		 */
		$retval['dir'] = $extractdir;

		/*
		 * Get the extension type and return the directory/type array on success or
		 * false on fail.
		 */
		$retval['type'] = self::detectType($extractdir);

		return ($retval['type']) ? $retval : false;
	}

	/**
	 * Method to detect the extension type from a package directory
	 *
	 * @param   string  $p_dir  Path to package directory
	 *
	 * @return  mixed  Extension type string or boolean false on fail
	 *
	 * @since   11.1
	 */
	public static function detectType($p_dir)
	{
		// Search the install dir for an XML file
		$files = JFolder::files($p_dir, '\.xml$', 1, true);

		if (!count($files))
		{
			JCKHelper::error(JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'));
			return false;
		}

		foreach ($files as $file)
		{
			if (!$xml = JCKHelper::getXML($file))
			{
				continue;
			}

			if ($xml->getName() != 'extension' && $xml->getName() != 'install')
			{
				unset($xml);
				continue;
			}

			$type = (string) $xml->attributes()->type;

			// Free up memory
			unset($xml);
			return $type;
		}

		JCKHelper::error(JText::_('JLIB_INSTALLER_ERROR_NOTFINDJOOMLAXMLSETUPFILE'));

		// Free up memory.
		unset($xml);
		return false;
	}
}