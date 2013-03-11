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

jimport('joomla.html.editor');

class JCKEditor extends JEditor
{
	/**
	 * Returns a reference to a global Editor object, only creating it
	 * if it doesn't already exist.
	 *
	 * This method must be invoked as:
	 * 		<pre>  $editor = &JEditor::getInstance([$editor);</pre>
	 *
	 * @access	public
	 * @param	string	$editor  The editor to use.
	 * @return	JEditor	The Editor object.
	 */
	function &getInstance($editor = 'jckeditor')
	{
		static $instances;

		if (!isset ($instances)) {
			$instances = array ();
		}

		$signature = serialize($editor);

		if (empty ($instances[$signature])) {
			$instances[$signature] = new JCKEditor($editor);
		}

		return $instances[$signature];
	}
	
	/**
	 * Load the editor
	 *
	 * @access	private
	 * @param	array	Associative array of editor config paramaters
	 * @since	1.5
	 */
	function _loadEditor($config = array())
	{
		//check if editor is already loaded
		if(!is_null(($this->_editor))) {
			return;
		}

		jimport('joomla.filesystem.file');

		// Build the path to the needed editor plugin
		$name = JFilterInput::clean($this->_name, 'cmd');
		$path = JPATH_SITE.DS.'plugins'.DS.'editors'.DS.$name.'.php';

		if ( ! JFile::exists($path) )
		{
			$message = JText::_('Cannot load the editor');
			JCKHelper::error( $message );
			return false;
		}

		// Require plugin file
		require_once $path;

		// Build editor plugin classname
		$name = 'plgEditor'.$this->_name;
			
		if($this->_editor = new $name ($this, $config))
		{
			// load plugin parameters
			$this->initialise();
			JPluginHelper::importPlugin('editors-xtd');
		}
	}



}