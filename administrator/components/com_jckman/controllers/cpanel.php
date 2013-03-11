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

class JCKManControllerCpanel extends JCKController
{
	protected $canDo = false;

	function __construct( $default = array())
	{
		parent::__construct( $default );

		$this->canDo = JCKHelper::getActions();
	}

	function check()
	{
		$this->display();
	}
	
	function sync()
	{
		if( !$this->canDo->get('jckman.sync') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=cpanel', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_SYNC' ), 'error' );
			return false;
		}

		$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=cpanel' ) );
	}
	
	function export()
	{
	}
}