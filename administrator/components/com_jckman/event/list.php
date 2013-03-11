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

jimport( 'joomla.event.event' );

class JCKListControllerListener extends JEvent
{
	protected $canDo 	= false;
	protected $app 		= false;

	function __construct( &$subject )
	{
		parent::__construct( $subject );

		$this->canDo 	= JCKHelper::getActions();
		$this->app 		= JFactory::getApplication();
	}

	/**
	 * A JParameter object holding the parameters for the plugin
	 *
	 * @var		A JParameter object
	 * @access	public
	 * @since	1.5
	 */
	 function onSave($plugin,$pluginToolbarnames)
	 {
		if( !$this->canDo->get('core.edit') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_SAVE' ), 'error' );
			return false;
		}

		require_once(CKEDITOR_LIBRARY.DS . 'toolbar.php');

		$CKfolder =  CKEDITOR_LIBRARY.DS . 'toolbar'; 

		jckimport('helper');
	    $toolbarnames = JCKHelper::getEditorToolbars();

		if(!empty( $toolbarnames))
		{
			 foreach($toolbarnames as $toolbarname)
			 {
				$tmpfilename = $CKfolder.DS.$toolbarname.'.php';

				require($tmpfilename);

				$classname = 'JCK'. ucfirst($toolbarname);

				$toolbar = new $classname();

				if(!$plugin->title)
				{
					//publish or unpblish plugin
					$this->onPublish(array($plugin->id),$plugin->published);
					return;
				}
				
				$pluginTitle =  str_replace(' ','',$plugin->title);
				$pluginTitle = 	$pluginTitle;

				//fix toolbar values or they will get wiped out
				foreach (get_object_vars( $toolbar ) as $k => $v)
				{
					if(is_null($v))
					{
						$toolbar->$k = ''; 
					}
					if($k[0] == '_')
						$toolbar->$k = NULL;
				}

				$toolbar->$pluginTitle = NULL;

				if(!empty($pluginToolbarnames) && in_array($toolbarname,$pluginToolbarnames))
					$toolbar->$pluginTitle = '';

				$toolbarConfig = new JRegistry('toolbar');

				$toolbarConfig->loadObject($toolbar);		

				// Get the config registry in PHP class format and write it to file
				if (!JFile::write($tmpfilename, $toolbarConfig->toString('PHP', array('class' => $classname . ' extends JCKToolbar')))) { 	  

					JCKHelper::error('Failed to add/remove '. $pname. 'plugin for the  ' . $classname .' toolbar');
				} 	  
			}

			//layout stuff
			$cids = array(0);
			
			$db =  JFactory::getDBO();

			if(!empty($pluginToolbarnames))
			{
				$values = array();
				foreach($pluginToolbarnames as $plugintoolbarname)
				{

					$query = 'SELECT id'
					. ' FROM #__jcktoolbars'
					. ' WHERE name = "'. $plugintoolbarname .'"';
					$db->setQuery( $query );
					$toolbarid = $db->loadResult();

					if($toolbarid)
					{
					
						$rowDetail = JCKHelper::getNextLayoutRow($toolbarid);

						$values[] = '('.$toolbarid.','. $plugin->id.','.$rowDetail->rowid.','.$rowDetail->rowordering.',1)';
					
						$cids[] = $toolbarid;
					}	
				}
			}	

			//First remove plugin from every layout that has not been selected
			$query = 'DELETE FROM #__jcktoolbarplugins'
				. ' WHERE pluginid ='. $plugin->id
				. ' AND toolbarid NOT IN (' . implode(',',$cids).')';
			$db->setQuery( $query );
			if (!$db->query()) {
				JCKHelper::error( $db->getErrorMsg() );
			}

			//Now add plugin to selected layouts
			if(!empty($values))
			{
				$query = 'INSERT INTO #__jcktoolbarplugins(toolbarid,pluginid,row,ordering,state) VALUES ' . implode(',',$values)
						.' ON DUPLICATE KEY UPDATE toolbarid = VALUES(toolbarid),pluginid = VALUES(pluginid)';
				$db->setQuery( $query );
				if(!$db->query()) 
				{
					JCKHelper::error( $db->getErrorMsg() );
				}
			}
		}	
		//publish or unpblish plugin
		$this->onPublish(array($plugin->id),$plugin->published);
	}

	function onPublish($cid,$value)
	{
		if( !$this->canDo->get('core.edit.state') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_PUB' ), 'error' );
			return false;
		}

		$db =  JFactory::getDBO(); 
		$user = JFactory::getUser();
		$cids = implode( ',', $cid );

		$query = 'SELECT name FROM #__jckplugins'
			. ' WHERE id IN ( '.$cids.' )'
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id').' ))'
			;
		$db->setQuery( $query );
		$pluginnames = $db->loadColumn();
		if (!$pluginnames) {
			JCKHelper::error( $db->getErrorMsg() );
		}

		jckimport('helper');

		$config = JCKHelper::getEditorPluginConfig();

		foreach($pluginnames as $pname)
			$config->set($pname,$value);

		$cfgFile = CKEDITOR_LIBRARY.DS . 'plugins' . DS . 'toolbarplugins.php'; 

		 // Get the toolbar registry in PHP class format and write it to file
		 $buffer = $config->toString('PHP', array('class' => 'JCKToolbarPlugins extends JCKPlugins'));
		 if (!JFile::write($cfgFile,$buffer)) { 	  
		 		  	
		 	  JCKHelper::error('Failed to '.($value ? 'publish ' : 'unpublish '). ' all selected jckeditor plugins');
		 } 	 
	}

	function onApply($plugin,$pluginToolbarnames)
	{
		if( !$this->canDo->get('core.edit') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_SAVE' ), 'error' );
			return false;
		}

		$this->onSave($plugin,$pluginToolbarnames);
	}

	function onUnpublish($cid,$value)
	{
		if( !$this->canDo->get('core.edit.state') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_UNPUB' ), 'error' );
			return false;
		}

		$this->onPublish($cid,$value);
	}
}