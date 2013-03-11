<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

require_once('nodes.php');

class menuLinkNodes extends linkNodes
{

	public function  __construct()
	{
		parent::__construct();
	}	
	
	
	
	public function getItems()
	{
	 	return $this->_getItems16();
	}
	
	
	private function _getItems16()
	{
		$db = JFactory::getDBO();
		$parent = JRequest::getVar('parent',0);
		$view = JRequest::getVar('view','types');
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());
			
		$query = '';
	
		if($view == 'menu')
		{
		
			$query = 'SELECT m.id, m.title AS name, m.link, m.id AS parent,'
			. ' 1 as expandible,'
			. ' 1 as selectable,'
			. ' 0 as doc_icon,'
			. ' "submenu" AS view'
			. ' FROM #__menu AS m'
			. ' INNER JOIN #__menu_types AS s ON s.menutype = m.menutype AND s.menutype =  "'. $parent .'"'
			. '	INNER JOIN  #__menu AS c on c.parent_id = m.id'
			. ' WHERE m.published = 1'
			. ' AND m.parent_id = 1'
			 .' AND  m.access IN ('.$groups.') '
			. ' UNION '
		    . '	SELECT m.id, m.title AS name, m.link, 0 AS parent,'
			. ' 0 as expandible,'
			. ' 1 as selectable,'
			. ' 1 as doc_icon,'
			. ' "submenu" AS view'
			. ' FROM #__menu AS m'
			. ' INNER JOIN #__menu_types AS s ON s.menutype = m.menutype AND s.menutype =  "'. $parent .'"'
			. '	LEFT JOIN  #__menu AS c on c.parent_id = m.id'
			. ' WHERE m.published = 1'
			. ' AND c.id IS NULL'
			. ' AND m.parent_id = 1'
			.' AND  m.access IN ('.$groups.') '
			. ' ORDER BY name';

		}
		elseif($view == 'submenu')
		{
			$query = 'SELECT m.id, m.title AS name, m.link, m.id AS parent,'
			. ' 1 as expandible,'
			. ' 1 as selectable,'
			. ' 0 as doc_icon,'
			. ' "submenu" AS view'
			. ' FROM #__menu AS m'
			. '	INNER JOIN  #__menu AS c on c.parent_id = m.id'
			. ' WHERE m.published = 1'
			. ' AND m.parent_id = '.(int) $parent
			.' AND  m.access IN ('.$groups.') '
			. ' UNION '
		    . '	SELECT m.id, m.title AS name, m.link, 0 AS parent,'
			. ' 0 as expandible,'
			. ' 1 as selectable,'
			. ' 1 as doc_icon,'
			. ' "submenu" AS view'
			. ' FROM #__menu AS m'
			. '	LEFT JOIN  #__menu AS c on c.parent_id = m.id'
			. ' WHERE m.published = 1'
			. ' AND m.parent_id = '.(int) $parent
			 .' AND  m.access IN ('.$groups.') '	
			. ' AND c.id IS NULL'
			. ' ORDER BY name';
		}
		else
		{
			
			$query = 'SELECT id,title AS name,"" AS link, menutype AS parent,'
			. ' 1 AS expandible, '
			. ' 0 as selectable,'
			. ' 0 as doc_icon,'
			. ' "menu" AS view'
			. ' FROM #__menu_types';
			
		}
		
		$db->setQuery($query); 
		$nodeList =  $db->loadObjectList();
		return $nodeList;
	}
	
		
	public function getLoadLink($node)
	{
		$config = JFactory::getConfig();
		$config->set('live_site','');
		return JURI::root() .'links.php?parent='.$node->parent.'&amp;extension=menu&amp;view='.$node->view;
	}
	
	public function getUrl($node)
	{
	 	return $this->_getUrl16($node);
	}
	
	private function _getUrl16($node)
	{
	 	$url = str_replace('&','&amp;',$node->link);
				
		if (preg_match('/^index.php/i', $url ) && strpos($url, 'Itemid') === false) {
					$url .= '&amp;Itemid=' . $node->id;
		}
	
		return $url;		
	}

}
?>