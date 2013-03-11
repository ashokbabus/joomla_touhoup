<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldJSCombo extends JFormFieldList
{
	protected $type = 'JSCombo';

	protected function getInput()
	{
		global $JElementJSComboJSWritten;
		if (!$JElementJSComboJSWritten) 
		{
			$file = dirname(__FILE__) . DS . "jscombo.js";
			$url  = str_replace(JPATH_ROOT, JURI::root(true), $file);
			$url  = str_replace(DS, "/", $url);
			$doc  = JFactory::getDocument();
			$doc->addScript( $url );
			$doc->addScriptDeclaration( 'jQuery(document).ready(function (){ new JSComboParam(); });' );	// Use jQuery to delay execution of our code till last

			$JElementJSComboJSWritten = TRUE;
		}

		$this->element['class'] = $this->element['class'] ? 'jck_combo' . chr( 32 ) .(string)$this->element['class'] : 'jck_combo';

		return parent::getInput();
	}
}