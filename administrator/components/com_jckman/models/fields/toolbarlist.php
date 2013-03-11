<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldToolbarList extends JFormFieldList 
{
	protected $type = 'ToolbarList';

	protected function getOptions()
	{
		$options 	= array();
		$items 		= JCKHelper::getEditorToolbars();

		// Build the field options.
		if(!empty($items))
		{
			foreach($items as $item)
			{
				$options[] = JHtml::_( 'select.option', $item, $item );
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}	