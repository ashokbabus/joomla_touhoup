<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2011 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// Do not allow direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class  plgSystemJCKModal extends JPlugin
{

	function plgSystemJCKModal(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onAfterInitialise()
	{
	
		$app = JFactory::getApplication();
		if( method_exists( 'JHTML', '_' ) && $app->isSite() )
		{
			JHtml::_('behavior.framework'); // -PC- [#2504]
			JHTML::_('behavior.modal');
		}//end 
	
	}//end function onAfterInitialise
}//end class plgSystemJCKModal