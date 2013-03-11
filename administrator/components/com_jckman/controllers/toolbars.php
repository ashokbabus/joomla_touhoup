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

class JCKManControllerToolbars extends JCKController
{
	protected $canDo = false;

	function __construct( $default = array())
	{
		parent::__construct( $default );

		$this->canDo = JCKHelper::getActions();

		$this->registerTask( 'apply', 		'save');
		$this->registerTask( 'edit', 		'display' );
		$this->registerTask( 'add', 		'display' );
		$this->registerTask( 'trash', 		'remove' );	// drop-down menu
		$this->registerTask( 'remove', 		'remove' );
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
				JRequest::setVar( 'view', 'toolbar' );
			}	break;
			case 'preview'	:
			{
				JRequest::setVar( 'view', 'toolbar' );
				JRequest::setVar( 'layout', 'popup' );
			} 
		}

		parent::display($cachable, $urlparams);
	}

	/**
	* Compiles information to add or edit a toolbar
	* @param string The current GET/POST option
	* @param integer The unique id of the record to edit
	*/
	function copy()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		if( !$this->canDo->get('core.create') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=toolbars', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_COPY' ), 'error' );
			return false;
		}

		// Initialize some variables
		$db 	= JFactory::getDBO();
		$app	= JFactory::getApplication();
		$cid	= $app->input->get( 'cid', array(), 'array' );
		$n		= count( $cid );

		if ($n == 0) {
			return JCKHelper::error( JText::_( 'No toolbars selected' ) );
		}

		$row 	=& JCKHelper::getTable('toolbar');
		$toolbarpugins	= array();
		
		$i = 1;	
		
		$ncid = array();

		foreach ($cid as $id)
		{
			// load the row from the db table
			$row->load( (int) $id );
			$row->title 		= 'Copy of ' . $row->title;
			$row->id 			= 0;
			$row->iscore 		= 0;
			$row->published 	= 1;
			$sql 				= $db->getQuery( true );
			$sql->select( 'count(1)' )
				->from( '#__jcktoolbars' )
				->where( 'title = "'. $row->title . '"' );

			//get offset for name of copy
			$offset		= $db->setQuery( $sql )->loadResult();
			$row->name 	= $row->name . ($offset +1);			
			
			if (!$row->check()) {
				return JCKHelper::error( $row->getError() );
			}
			if (!$row->store()) {
				return JCKHelper::error( $row->getError() );
			}

			$row->checkin();
			
			$ncid[] = $row->id; 
			$sql 	= $db->getQuery( true );
			$sql->select( 'pluginid,row,ordering,state' )
				->from( '#__jcktoolbarplugins' )
				->where( 'toolbarid = '. (int) $id );
			$rows = $db->setQuery( $sql )->loadObjectList();

			foreach ($rows as $toolbar_plugin_row) {
				$toolbarpugins[] = '('.(int) $row->id. ',' .(int) $toolbar_plugin_row->pluginid. ',' .(int) $toolbar_plugin_row->row. ','
				.(int) $toolbar_plugin_row->ordering. ','.(int) $toolbar_plugin_row->state.')';
			}
			
		}
		
		$this->event_args = array('cid' => $ncid);

		if (!empty( $toolbarpugins ))
		{
			// Toolbar-Plugin Mapping: Do it in one query
			$query = 'INSERT INTO #__jcktoolbarplugins (toolbarid,pluginid,row,ordering,state) VALUES '.implode( ',', $toolbarpugins );
			$db->setQuery( $query );
			if (!$db->query()) {
				return JCKHelper::error( $row->getError() );
			}
		}

		$msg = JText::sprintf( 'Items Copied', $n );
		$this->setRedirect( 'index.php?option=com_jckman&view=toolbars', $msg );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		if( !$this->canDo->get('core.edit') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=toolbars', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_SAVE' ), 'error' );
			return false;
		}
		
		$app = JFactory::getApplication();	

		$db 	= JFactory::getDBO();
		$row 	=& JCKHelper::getTable('toolbar');
		$task 	= $this->getTask();
		$form	= $app->input->get( 'jform', array(), 'array' );
		$form['rows'] = $app->input->get( 'rows', array(), 'array' );
		$components = $app->input->get( 'components', array(), 'array' );
		$params = array();
		$params['components'] = $components;
		$form['params'] = $params;

		$id = $app->input->get('id',0);

		$oldname = '';
		$isNew = false;

		if(!$id)
		{
			$isNew = true;
			$name = $form['name'];
			$form['name'] = str_replace(array(' ','-'),array('','_'),$name);			
		}
		else
		{
			$row->load((int)$id);
			$oldname = $row->name;
		}
		
			if (!$row->bind($form)) {
			JCKHelper::error( $row->getError() );
		}
		if (!$row->check()) {
			JCKHelper::error( $row->getError() );
		}
		if (!$row->store()) {
			JCKHelper::error( $row->getError() );
		}
		$row->checkin();

		//code to add plugins from layout
		$rows  = JRequest::getVar( 'rows', '', 'post');
		$rows = str_replace( ',/,,/,', ',/,', $rows );
		$rows = explode('/',$rows);

		if($rows[count($rows) -1] == ',')
			array_pop($rows);

		for($i = 0;$i < count($rows); $i++) $rows[$i] = explode(',',$rows[$i]);

		$values = array();
		$k = 1;
		$j = 1;
		$l = 1;

		$rowcount = count($rows );
		foreach($rows as $toolbar)
		{
			if(empty($toolbar))
				continue;
			
			foreach($toolbar as $icon)
			{
			   if($icon =='')
				continue;
			   
				if($icon ==';')
				{
					$k++;
					$j = 1;
				}
				else
				{
					$pluginid = str_replace('icon','',$icon);
					$values[] = '('.(int)$row->id.','.(int)$pluginid.','.$k.','.$j.',1)';
					$j++;
				}
			}
			$breakid = $l * -1;
			if($l < $rowcount)
				$values[] = '('.(int)$row->id.','.$breakid.','.$k.','.$j.',1)';
			$l++;
		}

		//first delete dependencies
		$query = 'DELETE FROM #__jcktoolbarplugins'
			. ' WHERE toolbarid = '.$row->id;

		$db->setQuery( $query );
		if (!$db->query()) {
			JCKHelper::error( $db->getErrorMsg() );
		}

		if(!empty($values))
		{
			$query = 'INSERT INTO `#__jcktoolbarplugins` (toolbarid,pluginid,row,ordering,state) VALUES ' . implode(',',$values);
			$db->setQuery( $query );
			if(!$db->query()) 
			{
				JCKHelper::error( $db->ErrorMsg() );
			}
		}

		//arguments for onSave Event
		$this->event_args = array('id' =>  $row->id,'name'=>$row->name,'oldname'=>$oldname,'title'=>$row->title,'isNew'=>$isNew);

		switch ( $task )
		{
			case 'apply':
				$msg = JText::sprintf( 'Successfully Saved changes to Toolbar %s', $row->title );
				$this->setRedirect( 'index.php?option=com_jckman&task=toolbars.edit&cid[]='. $row->id, $msg );
				break;

			case 'save':
			default:
				$msg = JText::sprintf( 'Successfully Saved Toolbar %s', $row->title );
				$this->setRedirect( 'index.php?option=com_jckman&view=toolbars', $msg );
				break;
		}
	}	

	function cancel()
	{
	  // Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		$app 	= JFactory::getApplication();
		$row 	= JCKHelper::getTable('toolbar');
		$form 	= $app->input->get( 'jform', array(), 'array' );
		$row->bind($form);
		$row->checkin();
       	$this->setRedirect( 'index.php?option=com_jckman&view=toolbars');
	}
		
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		if( !$this->canDo->get('core.delete') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=toolbars', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_SAVE' ), 'error' );
			return false;
		}

		$db		= JFactory::getDBO();
		$app	= JFactory::getApplication();
		$cid  	= $app->input->get( 'cid', array(0), 'array' );
		JArrayHelper::toInteger($cid, array(0));

		if (count( $cid ) < 1) {
			JCKHelper::error( JText::_( 'Select a Toolbar to delete' ) );
		}

		if (empty( $cid )) {
			return JCKHelper::error( 'No items selected' );
		}

		$cids = implode( ',', $cid );
		
		$editor = JPluginHelper::getPlugin('editors','jckeditor');
		$params =  new JRegistry($editor->params);
		$defaults = array(strtolower($params->get('toolbar','full')),strtolower($params->get('toolbar_ft','full')) );
		
		$sql  = $db->getQuery( true );
		$sql->select( 'count(1)' )
			->from( '#__jcktoolbars' )
			->where( 'id IN ('.$cids.')' )
			->where( 'LOWER(name)  IN ("' . implode('","',$defaults) .'")' );
		$total = $db->setQuery( $sql )->loadResult();
		if($msg = $db->getErrorMsg())
		{
			return JCKHelper::error( $msg);
		}
				
		if($total > 0){
			$this->setRedirect( 'index.php?option=com_jckman&view=toolbars');
			return JCKHelper::error('Default Toolbars cannot to be deleted' );
		}

				
		$sql  = $db->getQuery( true );
		$sql->select( 'count(1)' )
			->from( '#__jcktoolbars' )
			->where( 'id IN ('.$cids.')' )
			->where( 'iscore = 1' );
		$total = $db->setQuery( $sql )->loadResult();
		if($msg = $db->getErrorMsg())
		{
			return JCKHelper::error( $msg);
		}

		if($total > 0){
			$this->setRedirect( 'index.php?option=com_jckman&view=toolbars');
			return JCKHelper::error( 'Core Toolbars cannot to be deleted' );
		}

		$sql  = $db->getQuery( true );
		$sql->select( 'name' )
			->from( '#__jcktoolbars' )
			->where( 'id IN ('.$cids.')' );
		$rows = $db->setQuery( $sql )->loadColumn();

		if (!$db->query()) {
			return JCKHelper::error( $db->getErrorMsg() );
		}

		$this->event_args = array('names' => $rows);	

		//first delete dependencies
		$sql  = $db->getQuery( true );
		$sql->delete( '#__jcktoolbarplugins' )
			->where( 'toolbarid IN ('.$cids.')' );
		$db->setQuery( $sql );
		if (!$db->query()) {
			JCKHelper::error( $db->getErrorMsg() );
		}

		//delete toolbars
		$sql  = $db->getQuery( true );
		$sql->delete( '#__jcktoolbars' )
			->where( 'id IN ('.$cids.')' );
		$db->setQuery( $sql );
		if (!$db->query()) {
			JCKHelper::error( $db->getErrorMsg() );
		}

		$msg = JText::sprintf( 'Successfully Deleted Toolbars: %s', implode(',',$rows) );
		$this->setRedirect( 'index.php?option=com_jckman&view=toolbars',$msg );
	}

	function checkin()
	{
		// Check for request forgeries
		JRequest::checkToken() or die( 'Invalid Token' );

		if( !$this->canDo->get('core.edit.state') )
		{
			$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=toolbars', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_CHECK' ), 'error' );
			return false;
		}

		$db		= JFactory::getDBO();
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();
		$cid    = $app->input->get( 'cid', array(0), 'array' );
		$sql	= $db->getQuery( true );
		JArrayHelper::toInteger($cid, array(0));

		if(count( $cid ) < 1)
		{
			JCKHelper::error( JText::_( 'Select a toolbar to checkin' ) );
		}

		$cids = implode( ',', $cid );
		$sql->update( '#__jcktoolbars' )
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

		JCKHelper::error( (int)count( $cid ) . chr( 32 ) . 'toolbar' . $plural . ' checked in', 'message' );

		$this->setRedirect( JRoute::_( 'index.php?option=com_jckman&view=' . $app->input->get( 'view', 'toolbars' ), false ) );
	}
}