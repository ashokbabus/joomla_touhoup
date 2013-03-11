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

class JCKManViewtoolbar extends JViewLegacy
{
	protected $canDo;
	protected $app;
	protected $user;
	protected $item;
	protected $params;

	function display( $tpl = null )
	{
		$this->canDo		= JCKHelper::getActions();
		$this->app			= JFactory::getApplication();
		$this->user			= JFactory::getUser();
		$this->item			= '';
					
		$cid = $this->app->input->get( 'cid', array(), 'array' );
		JArrayHelper::toInteger($cid, array(0));

		if( !count( $cid ) && !$this->canDo->get('core.create') )
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_jckman&view=cpanel', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_CREATE' ), 'error' );
			return false;
		}
		elseif(!$this->canDo->get('core.edit'))
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_jckman&view=cpanel', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_EDIT' ), 'error' );
			return false;
		}//end if
		
	 
		$lists 	= array();
		$this->item = JCKHelper::getTable('toolbar');

		// load the row from the db table
		$this->item->load( $cid[0] );
		
		// fail if checked out not by 'me'
		if ($this->item->isCheckedOut( $this->user->get('id') ))
		{
			$msg = JText::sprintf( 'COM_JCKMAN_MSG_BEING_EDITED', JText::_( 'The toolbar' ), $this->item->title );
			$this->app->redirect( JRoute::_( 'index.php?option=com_jckman&view=toolbars', false ), $msg, 'error' );
			return false;
		}

		if ($cid[0])
		{
			$this->item->checkout( $this->user->get('id') );
			
			//now lets get default toolbars
			$editor = JPluginHelper::getPlugin('editors','jckeditor');
			$params =  new JRegistry($editor->params);
			$this->default = $params->get('toolbar','full'); 
			$this->defaultFT = $params->get('toolbar_ft','full');
			
			if(strtolower($this->item->name) == strtolower($this->default) || strtolower($this->item->name) == strtolower($this->defaultFT))
				$this->item->default = true;
			else
				$this->item->default = false;
		} 
		else {
			$this->item->params  = '';
			$this->item->default = false;
			
		}

		$db = JFactory::getDBO();

		//set the default total number of plugin records
		$total = 0;
		$totalRows = 0;
		
		if ( $cid[0] )
		{
			$total = 1;

			$sql = $db->getQuery( true );
			$sql->select( 'p.id,p.name,p.title,p.icon,tp.row' )
				->from( '#__jckplugins p' )
				->join( 'INNER', '#__jcktoolbarplugins tp ON tp.pluginid = p.id' )
				->join( 'LEFT', '#__jckplugins parent on parent.id = p.parentid' )
				->where( 'tp.state = 1' )
				->where( 'tp.toolbarid = '.(int) $this->item->id )
				->where( 'p.published = 1' )
				->where( '(p.parentid IS NULL OR parent.published = 1)' )
				->order( 'tp.toolbarid ASC,tp.row ASC,tp.ordering ASC' );
			$toolbarplugins = $db->setQuery( $sql )->loadObjectList();

			// get the total number of core plugin records
			$sql = $db->getQuery( true );
			$sql->select( 'COUNT(*)' )
				->from( '#__jcktoolbarplugins tp' )
				->join( 'INNER', '#__jckplugins p ON tp.pluginid = p.id' )
				->join( 'LEFT', '#__jckplugins parent on parent.id = p.parentid' )
				->where( 'tp.toolbarid ='.(int) $this->item->id )
				->where( 'p.iscore = 1' );
			$totalRows = $db->setQuery( $sql )->loadResult();

			if(!$totalRows) //lets get plugins from class file
			{
				require_once(CKEDITOR_LIBRARY.DS . 'toolbar.php');
				$CKfolder =  CKEDITOR_LIBRARY.DS . 'toolbar'; 
				$filename = $CKfolder.DS.$this->item->name.'.php';	
				require($filename);
				$classname = 'JCK'. ucfirst($this->item->name);
				$toolbar = new $classname();

				$sql = $db->getQuery( true );
				$sql->select( 'p.id, p.title' )
					->from( '#__jckplugins p' )
					->join( 'LEFT', '#__jckplugins parent on parent.id = p.parentid' )
					->where( 'parent.published = 1' )
					->where( 'p.title != ""' )
					->where( 'p.published = 1' )
					->where( 'p.iscore = 1' )
					->where( '(p.parentid IS NULL OR parent.published = 1)' );
				$allplugins = $db->setQuery( $sql )->loadObjectList();

				$values = array();
				//fix toolbar values or they will get wiped out
				$l = 1;
				$n = 1;
				$j = 1;

				foreach (get_object_vars( $toolbar ) as $k => $v)
				{
					if($v) 
					{
						$n = ($n > $v ? $n :  $v);
					}
					if($l < $n)
					{
						$l = $n;
						$j = 1;
					}

					for($m = 0; $m < count($allplugins); $m++)
					{
						if($k == $allplugins[$m]->title)
						{
							$values[] = '('.(int)$this->item->id.','.(int)$allplugins[$m]->id.','.$n.','.$j.',1)';
							break;
						}

						if(strpos($k,'brk_') !== false)
						{
							$id = preg_match('/[0-9]+$/',$k);
							$id = $id * -1;
							$values[] = '('.(int)$this->item->id.','.$id.','.$n.','.$j.',1)';
							$n++;
							break;
						}
					}
					$j++;
				}

				if(!empty($values))
				{
					$query = 'INSERT INTO #__jcktoolbarplugins(toolbarid,pluginid,row,ordering,state) VALUES ' . implode(',',$values);
					$db->setQuery( $query );
					if(!$db->query())
					{
						JCKHelper::error( $db->getErrorMsg() );
					}
				}
			}

			$sql = $db->getQuery( true );
			$sql->select( 'p.id,p.name,p.title,p.icon,p.row' )
				->from( '#__jckplugins p' )
				->join( 'LEFT', '#__jcktoolbarplugins tp ON tp.pluginid = p.id AND tp.toolbarid = '.(int) $this->item->id )
				->join( 'LEFT', '#__jckplugins parent on parent.id = p.parentid' )
				->where( 'tp.pluginid is null' )
				->where( 'p.published = 1' )
				->where( 'p.title != ""' )
				->where( 'p.iscore = 1' )
				->where( '(p.parentid IS NULL OR parent.published = 1)' )
				->order( 'p.row ASC, p.id ASC' );
			$plugins = $db->setQuery( $sql )->loadObjectList();

			$sql = $db->getQuery( true );
			$sql->select( 'tp.pluginid AS id,p.name,p.title,p.icon,tp.row' )
				->from( '#__jcktoolbarplugins tp' )
				->join( 'LEFT', '#__jckplugins p ON tp.pluginid = p.id AND p.published = 1' )
				->join( 'LEFT', '#__jckplugins parent on parent.id = p.parentid AND parent.published = 1' )
				->where( 'tp.state = 1' )
				->where( 'tp.toolbarid = '.(int)$this->item->id )
				->where( '(p.parentid IS NULL OR parent.published = 1)' )
				->order( 'tp.toolbarid ASC,tp.row ASC,tp.ordering ASC' );
			$toolbarplugins = $db->setQuery( $sql )->loadObjectList();	
			$toolbarplugins = $this->_getSortRowToolbars($toolbarplugins);

			$this->assignRef('toolbarplugins',	$toolbarplugins);
			$this->assignRef('plugins',	$plugins );
		}

		
		//
		$params = new JRegistry($this->item->params);
		
		$components = $params->get('components',array());
		
		$db->setQuery("SELECT element as value, REPLACE(element,'com_','')  as text FROM #__extensions WHERE type = 'component' ORDER BY element ASC");
		$allcomponents =  $db->loadObjectList();		
		$lists['components'] = JHTML::_('select.genericlist',  $allcomponents, 'components[]', ' size="10" multiple', 'value', 'text', $components);
				
		$this->assignRef('lists',	$lists);
		$this->assignRef('toolbar', $this->item);
		$this->assignRef('total', $total);

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$this->app->input->set('hidemainmenu', true);

		$bar 		= JToolBar::getInstance('toolbar');
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $this->user->get('id'));

		JToolBarHelper::title( JText::_( 'Layout Manager' ) .':' . chr( 32 ) . JText::_($this->item->name), 'plugin.png' );

		if( $this->canDo->get('core.create') && !$checkedOut )
		{
			JToolBarHelper::apply( 'toolbars.apply' );
			JToolBarHelper::save( 'toolbars.save' );
		}//end if

    	JToolBarHelper::cancel( 'toolbars.cancel', 'JTOOLBAR_CLOSE' );

		JCKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function
		
	function _getSortRowToolbars($toolbars)
	{
		$out = array();
		$count = 0;
		$outToolbars = array();
		$results = array();
		
		for($i = 0; $i < count($toolbars);$i++)
		{
			if($toolbars[$i]->id >= 0 )
			{  
				$out[] = $toolbars[$i];
			}

			if($toolbars[$i]->id < 0)
			{
				$outToolbars[] = $out;
				$out = array();
			}
		}		

		if(!empty($out))
		  $outToolbars[] = $out;	

		$results =  $outToolbars;

		//lets add spacer to each row
		$spacer =  new stdclass;
		$spacer->title = 'spacer';
		$spacer->name = 'spacer';
		$spacer->id = 0;

		for($n= 0; $n < count($results);$n++)
		{
			$result = $results[$n];
			$out = array();
			$rowNumber = $results[$n][0]->row;
			foreach($result as $icon)
			{
				if($icon->row > $rowNumber)
					$out[] =  $spacer;
				$out[] = $icon;
				$rowNumber = $icon->row;  
			}
			$results[$n] = $out;
		}

		return $results;
	}
}