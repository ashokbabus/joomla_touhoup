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
require_once(dirname(__FILE__).DS.'extension.php');

class JCKManModelPlugin extends JCKManModelInstaller
{
	/**
	 * Extension Type
	 * @var	string
	 */
	var $_type = 'plugin';

	/**
	 * Overridden constructor
	 * @access	protected
	 */
	function __construct()
	{
		$app = JFactory::getApplication();
			
		// Call the parent constructor
		parent::__construct();

		// Set state variables from the request
		$this->setState('filter.string', $app->getUserStateFromRequest( "com_jckman.plugin.string", 'filter', '', 'string' ));
	}

	function _loadItems()
	{
		// Get a database connector
		$db  = JFactory::getDBO();
		$sql = $db->getQuery( true );
		$sql->select( 'id, title, type, name' )
			->from( '#__jckplugins' )
			->where( 'type = "plugin"' )
			->where( 'iscore = 0' )
			->order( 'name' );

		if($search = $this->state->get('filter.string'))
		{
			$sql->where( 'title LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) );
		}

		$rows = $db->setQuery($sql)->loadObjectList();
		// Get the plugin base path
		$baseDir = JCK_PLUGINS;

		$numRows = count($rows);

		for ($i = 0; $i < $numRows; $i ++)
		{
			$row = & $rows[$i];

			// Get the plugin xml file
			$xmlfile = $baseDir .DS. $row->name .DS. $row->name .".xml";

			if (file_exists($xmlfile)) {
				if ($data = JCKHelper::parseXMLInstallFile($xmlfile)) {
					foreach($data as $key => $value)
					{
						if($value)
							$row->$key = $value;
					}
				}
			}
		}
		$this->setState('pagination.total', $numRows);
		if($this->state->get('pagination.limit') > 0) {
			$this->_items = array_slice( $rows, $this->state->get('pagination.offset'), $this->state->get('pagination.limit') );
		} else {
			$this->_items = $rows;
		}
	}
}