<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

jimport('joomla.installer.installer');

class JCKInstaller extends JInstaller
{
	/**
	 * Returns a reference to the global Installer object, only creating it
	 * if it doesn't already exist.
	 *
	 * @static
	 * @return	object	An installer object
	 * @since 1.5
	 */
	public static function &getInstance()
	{
		static $instance;

		if (!isset ($instance)) {
			$instance = new JCKInstaller();
		}
		return $instance;
	}

	/**
	 * Set an installer adapter by name
	 *
	 * @access	public
	 * @param	string	$name		Adapter name
	 * @param	object	$adapter	Installer adapter object
	 * @return	boolean True if successful
	 * @since	1.5
	 */
	public function setAdapter($name, &$adapter = null,$options = Array())
	{
		// Check if valid extension type
		if( $name == 'plugin' || $name == 'language' || $name == 'skin'){
			if (!is_object($adapter))
			{			
				// Try to load the adapter object
				require_once(dirname(__FILE__).DS. '..'.DS.'adapters'.DS.strtolower($name).'.php');
				$class = 'JCKInstaller'.ucfirst($name);
				if (!class_exists($class)) {
					return false;
				}
				$adapter = new $class($this);
				$adapter->parent =& $this;
			}
			$this->_adapters[$name] = $adapter;
			return true;
		}else{
			$this->abort(JText::_('Incorrect version!'));
		}
	}

	/**
	 * Is the XML file a valid Joomla installation manifest file.
	 *
	 * @param   string  $file  An xmlfile path to check
	 *
	 * @return  mixed  A JXMLElement, or null if the file failed to parse
	 *
	 * @since   11.1
	 */
	public function isManifest($file)
	{
		// Initialise variables.
		$xml = JFactory::getXML($file);

		// If we cannot load the XML file return null
		if (!$xml)
		{
			return null;
		}

		// Check for a valid XML root tag.
		// @todo: Remove backwards compatibility in a future version
		// Should be 'extension', but for backward compatibility we will accept 'extension' or 'install'.

		// 1.5 uses 'install'
		// 1.6 uses 'extension'
		if ($xml->getName() != 'install' && $xml->getName() != 'extension')
		{
			return null;
		}

		// Valid manifest file return the object
		return $xml;
	}

	/**
	 * Tries to find the package manifest file
	 *
	 * @return  boolean  True on success, False on error
	 *
	 * @since 11.1
	 */
	public function findManifest()
	{
		// Get an array of all the XML files from the installation directory
		$xmlfiles = JFolder::files($this->getPath('source'), '.xml$', 1, true);

		// If at least one XML file exists
		if (!empty($xmlfiles))
		{

			foreach ($xmlfiles as $file)
			{
				// Is it a valid Joomla installation manifest file?
				$manifest = $this->isManifest($file);

				if (!is_null($manifest))
				{
					// If the root method attribute is set to upgrade, allow file overwrite
					if ((string) $manifest->attributes()->method == 'upgrade')
					{
						$this->upgrade = true;
						$this->overwrite = true;
					}

					// If the overwrite option is set, allow file overwriting
					if ((string) $manifest->attributes()->overwrite == 'true')
					{
						$this->overwrite = true;
					}

					// Set the manifest object and path
					$this->manifest = $manifest;
					$this->setPath('manifest', $file);

					// Set the installation source path to that of the manifest file
					$this->setPath('source', dirname($file));

					return true;
				}
			}

			// None of the XML files found were valid install files
			JCKHelper::error(JText::_('JLIB_INSTALLER_ERROR_NOTFINDJOOMLAXMLSETUPFILE'));

			return false;
		}
		else
		{
			// No XML files were found in the install folder
			JCKHelper::error(JText::_('JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE'));
			return false;
		}
	}
}

//dummy class that does nothing
class InstallerHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName = 'install')
	{

	}
}
