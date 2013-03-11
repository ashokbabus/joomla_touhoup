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

class JCKTablePlugin extends JTable
{
	/**
	 * Primary Key
	 *
	 *  @var int
	 */
	var $id = null;

	/**
	 * @var varchar
	 */
	var $title = null;

	/**
	 * @var varchar
	 */
	var $name = null;

	/**
	 * @var varchar
	 */
	var $type = null;
	
	/**
	 * @var varchar
	 */
	var $icon = null;
	
	/**
	 * @var tinyint
	 */
	var $published = null;
	
	/**
	 * @var tinyint
	 */
	var $editable = 1;

	/**
	 * @var int
	 */
	var $checked_out = 0;

	/**
	 * @var datetime
	 */
	var $checked_out_time = 0;

	/**
	 * @var tinyint unsigned
	 */
	 var $iscore = null;

	/**
	 * @var text
	 */
	 var  $acl = null;

	 /**
	 * @var text
	 */ 
	 var $params = null;

	function __construct(& $db) {
		parent::__construct('#__jckplugins', 'id', $db);
	}

	public function setParent($pluginName = '')
	{
		if($pluginName)
		{
			// Build the query to get the asset id for the parent category.
			$sql = $this->_db->getQuery(true);
			$sql->select('id')
				->from('#__jckplugins')
				->where('name = "'.$pluginName. '"');

			$id = $this->_db->setQuery($sql)->loadResult(); 	

			// Return the asset id.
			if($id)
			{
				$this->parentid = $id;
			}
		}
	}

	function bind($array, $ignore = '')
	{
		if (isset( $array['params'] ) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		return parent::bind($array, $ignore);
	}
}