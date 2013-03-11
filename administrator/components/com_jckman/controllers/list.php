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

class JCKManControllerList extends JCKController
{
	protected $canDo = false;

	function __construct( $default = array())
	{
		parent::__construct( $default );

		$this->canDo = JCKHelper::getActions();

		$this->registerTask( 'apply', 		'save');
		$this->registerTask( 'unpublish', 	'publish');
		$this->registerTask( 'edit', 		'display' );
		$this->registerTask( 'add', 		'display' );
		$this->registerTask( 'orderup', 	'order' );
		$this->registerTask( 'orderdown', 	'order' );
	}

	function display($cachable = false, $urlparams = false )
	{
		switch($this->getTask())
		{
			case 'add'     :
			case 'edit'    :
			{
				JRequest::setVar( 'hidemainmenu', 1 );
				JRequest::setVar( 'layout', 'form' );
				JRequest::setVar( 'view', 'editplugin' );
			}
		}

		parent::display($cachable, $urlparams);
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		if( !$this->canDo->get('core.edit') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_SAVE' ), 'error' );
			return false;
		}

		$app	= JFactory::getApplication();
		$db 	= JFactory::getDBO();
		$row 	= JCKHelper::getTable('plugin');
		$task 	= $this->getTask();	
		$post 	= $app->input->get('jform', array(), 'array');
		$params	= $app->input->get('params', array(), 'array');
	//	$params = ( array_key_exists( 'params', $post ) ) ? $post['params'] : array();
		$groups = ( array_key_exists( 'groups', $post ) ) ? $post['groups'] : array();

		$post['params'] = $params;

		JArrayHelper::toInteger($groups);

		if (!$row->bind($post)) {
			JCKHelper::error( $row->getError() );
		}

		$row->acl = json_encode($groups); //AW
		
		if (!$row->check()) {
			JCKHelper::error( $row->getError() );
		}

		if (!$row->store()) {
			JCKHelper::error( $row->getError() );
		}

		$row->checkin();

		//$row->reorder( 'type = '.$db->Quote($row->type).' AND ordering > -10000 AND ordering < 10000');

		//update toolbar selections so set args for event
		$selections = ( array_key_exists( 'selections', $post ) ) ? $post['selections'] : array();
		$this->event_args = array('plugin' => $row,'pluginToolbarnames'=>$selections );

		switch ( $task )
		{
			case 'apply':
				$msg = JText::sprintf( 'COM_JCKMAN_PLUGIN_SAVE_CHANGES', ( $row->title ?: $row->name ) );
				$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&task=list.edit&cid[]='. $row->id, false ), $msg );
				break;

			case 'jcksave':
			default:
				$msg = JText::sprintf( 'COM_JCKMAN_PLUGIN_SAVE', ( $row->title ?: $row->name ) );
				$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), $msg );
				break;
		}
	}	

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		if( !$this->canDo->get('core.edit.state') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_STATE' ), 'error' );
			return false;
		}

		$app 		= JFactory::getApplication();
		$db			= JFactory::getDBO();
		$user		= JFactory::getUser();
		$cid     	= $app->input->get( 'cid', array(), 'array' );
		JArrayHelper::toInteger($cid, array(0));
		$publish 	= ( $this->getTask() == 'publish' ? 1 : 0 );
		$action 	= $publish ? JText::_( 'publish' ) : JText::_( 'unpublish' );

		if (count( $cid ) < 1) {
			JCKHelper::error( JText::_( 'Select a plugin to '.$action ) );
		}

		$cids = implode( ',', $cid );
		$sql = $db->getQuery( true );
		$sql->update( '#__jckplugins' )
			->set( 'published = '.(int) $publish )
			->where( 'id IN ( '.$cids.' )' )
			->where( '( checked_out = 0 OR ( checked_out = '.(int) $user->get('id').' ))' );

		if(!$db->setQuery( $sql )->query())
		{
			JCKHelper::error( $db->getErrorMsg() );
		}

		$this->event_args 	= array('cid' => $cid,'value'=>$publish );
		$plural				= ( count( $cid ) > 1 ) ? '(s)' : '';

		JCKHelper::error( (int)count( $cid ) . chr( 32 ) . 'plugin' . $plural . chr( 32 ) . $action . 'ed', 'message' );

		$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ) );
	}

	function cancel()
	{
	  // Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$app 	= JFactory::getApplication();
		$form 	= $app->input->get( 'jform', array(), 'array' );
		$row 	= JCKHelper::getTable('plugin');
		$row->bind( $form );
		$row->checkin();

		$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ) );
	}
	
	function order()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ) );
	}

	function saveorder()
	{
		//Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );
		$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), $msg );
	}

	function checkin()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		if( !$this->canDo->get('core.edit.state') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_CHECK' ), 'error' );
			return false;
		}

		$db		= JFactory::getDBO();
		$user	= JFactory::getUser();
		$cid    = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$sql	= $db->getQuery( true );
		JArrayHelper::toInteger($cid, array(0));

		if(count( $cid ) < 1)
		{
			JCKHelper::error( JText::_( 'Select a plugin to checkin' ) );
		}

		$cids = implode( ',', $cid );
		$sql->update( '#__jckplugins' )
			->set( array( 'checked_out = 0', 'checked_out_time = "0000-00-00 00:00:00"' ) )
			->where( 'id IN ( ' . $cids . ' )' )
			->where( 'checked_out = ' . (int)$user->get('id') );
		$db->setQuery( $sql );

		if( !$db->query() )
		{
			JCKHelper::error( $db->getErrorMsg() );
		}

		$this->event_args 	= array('cid' => $cid,'value'=> true );
		$plural				= ( count( $cid ) > 1 ) ? '(s)' : '';

		JCKHelper::error( (int)count( $cid ) . chr( 32 ) . 'plugin' . $plural . ' checked in', 'message' );

		$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ) );
	}
}