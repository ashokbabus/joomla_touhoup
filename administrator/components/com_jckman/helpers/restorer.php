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

class JCKRestorer extends JInstaller
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
			$instance = new JCKRestorer();
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
	public function setAdapter($name, &$adapter = null,$options = array())
	{
		// Check if valid extension type
		if( $name == 'plugin' || $name == 'language' || $name == 'skin' || $name== 'backup'){
			if (!is_object($adapter))
			{			
				// Try to load the adapter object
				require_once(dirname(__FILE__).DS. '..'.DS.'restorers'.DS.strtolower($name).'.php');
				$class = 'JCKRestorer'.ucfirst($name);
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
	 * @return  mixed  A SimpleXMLElement, or null if the file failed to parse
	 *
	 * @since   11.1
	 */
	public function isManifest($file)
	{
		$xml = simplexml_load_file($file);

		// If we cannot load the XML file return null
		if (!$xml)
		{
			return null;
		}

		// Check for a valid XML root tag.
		if ($xml->getName() != 'extension' && $xml->getName() != 'install')
		{
			return null;
		}

		// Valid manifest file return the object
		return $xml;
	}
		
}