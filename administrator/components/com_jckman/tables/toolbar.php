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

class JCKTableToolbar extends JTable
{
	/**
	 * Primary Key
	 *
	 *  @var int
	 */
	var $id = null;

	/**
	 *
	 *
	 * @var varchar
	 */
	var $title = null;

	/**
	 *
	 *
	 * @var varchar
	 */
	var $name = null;

	/**
	 *
	 *
	 * @var tinyint
	 */
	var $published = null;
	
	/**
	 *
	 *
	 * @var tinyint
	 */

	var $checked_out = 0;

	/**
	 *
	 *
	 * @var datetime
	 */
	var $checked_out_time = 0;

	/**
	 *
	 *
	 * @var text
	 */
	 var $iscore = null;
   /**
	 *
	 *
	 * @var int unsigned
	 */
	 var $params = null;
	
	/**
	 *
	 *
	 * @var text
	 */   

	function __construct(& $db) {
		parent::__construct('#__jcktoolbars', 'id', $db);
	}
	
  /**
	* Overloaded bind function
	*
	* @access public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
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
?>