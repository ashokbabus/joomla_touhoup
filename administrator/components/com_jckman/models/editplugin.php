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

class JCKManModelEditPlugin extends JModelForm
{
	protected $item;

	public function getItem( $pk = null )
	{
		$user		= JFactory::getUser();
		$app		= JFactory::getApplication();
		$cid 		= $app->input->get( 'cid', array(), 'array' );
		$id 		= current( $cid );
		$item 		= JCKHelper::getTable('plugin');

		// load the row from the db table
		$item->load( $id );
		
		// Hide CK's plugin
		if( !$item || in_array( $item->name, JCKHelper::getHiddenPlugins() ) )
		{
			$app->redirect( 'index.php?option=com_jckman&view=list', 'Could Not Load Plugin.', 'error' );
			return false;		
		}

		// fail if checked out not by 'me'
		if ($item->isCheckedOut( $user->get('id') ))
		{
			$msg = JText::sprintf( 'COM_JCKMAN_MSG_BEING_EDITED', JText::_( 'The plugin' ), ($item->title ?: $item->name) );
			$app->redirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), $msg, 'error' );
			return false;
		}

		// TOOLBARS
		$toolbars = $this->getToolbarList();
		$item->selections = $this->getSelectedToolbarList();

		if( !$item->selections )
		{
			$item->toolbars = 'none';
		}
		elseif( count( $item->selections ) == count( $toolbars ) )
		{
			$item->toolbars = 'all';
		}
		else
		{
			$item->toolbars = 'select';
		}

		// GROUPS
		$groups 		= $this->getUserGroupList();
		$allowedGroups 	= array();
		
		// re-order groups to match acl col
		foreach( $groups as $group )
		{
			$allowedGroups[] = $group->value;
		}

		if( !is_null( $item->acl ))
		{
			$allowedGroups = json_decode($item->acl);
		}

		if($item->acl == '[]')
		{
			$item->group = 'special';
		} 
		elseif(count($allowedGroups) == count($groups)) 
		{
			$item->group = 'all';
		} 
		else 
		{
			$item->group = 'select';
		}

		$item->groups	= $allowedGroups;
		$xmlPath = '';

		if($item->iscore) //AW ger path for core plugins XML file
		{
			$path		= JPATH_COMPONENT.DS.'editor'.DS.'plugins';
			$xmlPath 	= $path .DS. $item->name .'.xml';
	    }
		else
		{
			$path		= JPATH_PLUGINS .DS. 'editors' .DS. 'jckeditor' .DS. 'plugins' .DS. $item->name;
			$xmlPath 	= $path .DS. $item->name .'.xml';
		}

		if($id)
		{
			$item->checkout( $user->get('id') );
   
            if(JFile::exists($xmlPath ))
			{
	            $data = JApplicationHelper::parseXMLInstallFile( $xmlPath );
				$item->description = $data['description'];	
			}
			else
			{
				$item->description = '';
			}
		} else {
			$item->type 		= 'plugin';
			$item->published 	= 1;
			$item->description 	= 'From XML install file';
			$item->icon 		= '';
			$item->params		= '';
		}

		$this->item = $item;

		return $this->item;
	}

	function getForm( $data = array(), $loadData = true )
	{
		$form = $this->loadForm('com_jckman.editplugin', JPATH_COMPONENT_ADMINISTRATOR . '/models/form/editplugin.xml', array('control' => 'jform', 'load_data' => $loadData));

		return ( empty( $form ) ) ? false : $form;
	}

	// Not yet in use.... (swap out for what the view is doing someday?
	function getPluginForm( $data = false, $loadData = true )
	{
		$form = $this->loadForm('com_jckman.plugin', $data, array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	function getSelectedToolbarList()
	{
		return JModelLegacy::getInstance( 'list', 'JCKManModel' )->getSelectedToolbarList();
	}

	function getToolbarList()
	{
		return JModelLegacy::getInstance( 'install', 'InstallerModel' )->getToolbarList();
	}

	function getUserGroupList()
	{
		return JModelLegacy::getInstance( 'list', 'JCKManModel' )->getUserGroupList();
	}
}