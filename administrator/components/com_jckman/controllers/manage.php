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

class JCKManControllerManage extends JControllerAdmin
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Remove an extension (Uninstall).
	 *
	 * @return	void
	 * @since	1.5
	 */
	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();
		$cid	= $app->input->get( 'cid', array(), 'array' );
		$model 	= $this->getModel( 'Manage' );
		JArrayHelper::toInteger( $cid, array() );
		$result = $model->remove( $cid );
		$view 	= $app->input->get( 'view', 'plugin' );
	
		$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=' . $view, false ) );
	}
}