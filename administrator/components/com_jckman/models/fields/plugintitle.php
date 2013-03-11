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
JFormHelper::loadFieldClass('text');

/**
 * Form Field class for the Joomla Platform. Load up the JCK's toolbars
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 *
 */
class JFormFieldPluginTitle extends JFormFieldText 
{
	protected $type = 'PluginTitle';

	protected function getInput()
	{
		$model 	= JModelLegacy::getInstance( 'editplugin', 'JCKManModel' );
		$item	= $model->getItem();

		if( $item )
		{
			if( !$item->title )
			{
				$this->element['readonly'] = 'true';
				$this->element['disabled'] = 'true';
			}//end if
		}//end if

		return parent::getInput();
	}
}	