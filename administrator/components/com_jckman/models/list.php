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

class JCKManModelList extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'title' => 'p.title',
				'published' => 'p.published',
				'name' => 'p.name',
				'icon' => 'p.icon',
				'id' => 'p.id'
			);
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$iscore = $this->getUserStateFromRequest($this->context.'.filter.iscore', 'filter_iscore', '', 'string');
		$this->setState('filter.iscore', $iscore);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_jckman');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('id', 'DESC');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.iscore');

		return parent::getStoreId($id);
	}	

	protected function getListQuery()
	{
		// Create a new query object.
		$hide	= JCKHelper::getHiddenPlugins( true );
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select(
			$this->getState(
				'list.select',
				'p.*, u.name AS editor'
			)
		);
		$query->from('#__jckplugins AS p');
		$query->join('LEFT', '#__users AS u ON u.id = p.checked_out');
		$query->where( 'p.type IN ("plugin","filebrowser")' );
		$query->group('p.id');

		// Filter by published state
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('p.published = '.(int) $state);
		}
		elseif ($state === '') {
			$query->where('(p.published IN (0, 1))');
		}

		// Filter by is core plugin
		$iscore = $this->getState('filter.iscore');
		if($iscore != '') {
			$query->where('p.iscore = '.(int)$iscore);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0) {
				$query->where('p.id = '.(int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('p.name LIKE '.$search.' OR p.title LIKE '.$search);
			}
		}
		// Hide CK's plugin
		$query->where('p.name NOT IN ( ' . $hide . ' )');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'p.id');
		$orderDirn	= $this->state->get('list.direction', 'DESC');
		$query->order($db->escape($orderCol . chr( 32 ) . $orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	/**
	 * Method to get a hello
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__jckplugins'.
					'  WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
		}
	   	
		return $this->_data;
	}
	
	function &getTypes()
	{
	 $query = 'SELECT type AS value, type AS text'
		. ' FROM #__jckplugins'
		. ' GROUP BY type'
		. ' ORDER BY type'
		;
	 $this->_db->setQuery( $query );
	 $types = $this->_db->loadObjectList();
	 return  $types; 

	 }		

	function getSelectedToolbarList()
	{
		$rows = array();
		jckimport('helper');
		$toolbars = JCKHelper::getEditorToolbars();

		$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
		JArrayHelper::toInteger($cid, array(0));	

		$db  = JFactory::getDBO();
		$sql = $db->getQuery(true);
		$sql->select( 'title' )
			->from( '#__jckplugins' )
			->where( 'id = '. $cid[0] );

		$pluginname = $db->setQuery( $sql )->loadResult();

		if (!!$pluginname && !is_string($pluginname) ) {
			JCKHelper::error( $db->getErrorMsg() );
		}

		jckimport('helper');
		$toolbarnames = JCKHelper::getEditorToolbars();

		if(!empty($toolbarnames))
		{
			require_once(CKEDITOR_LIBRARY.DS . 'toolbar.php');

			$CKfolder = CKEDITOR_LIBRARY.DS . 'toolbar'; 

			foreach($toolbarnames as $toolbarname)
			{
				$tmpfilename = $CKfolder.DS.$toolbarname.'.php';

				require_once($tmpfilename);

				$classname = 'JCK'. ucfirst($toolbarname);

				$toolbar = new $classname();
				$pluginTitle = str_replace(' ','',$pluginname);
				//$pluginTitle = ucfirst($pluginTitle); leave it to plugin XML to captialize titile
				if(!isset($toolbar->$pluginTitle)) continue;		
				$row = new stdclass;
				$row->text = $toolbarname;
				$row->value = $toolbarname;
				$rows[] = $row;
			}
		}

		return $rows;
	}

	function getUserGroupList()
	{
		$db  = JFactory::getDBO();
		$sql = $db->getQuery(true);
		$sql->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level')
			->from($db->quoteName('#__usergroups') . ' AS a')
			->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id, a.title, a.lft, a.rgt')
			->order('a.lft ASC');

		$options = $db->setQuery($sql)->loadObjectList();

		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
		}

		return $options;
	}
}