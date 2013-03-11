<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Install Model
 *
 * @package    JCK Editor
 * @subpackage JCK.install Wizard
  */
 
JCKLoader::loadExtendClass('model');   
  
class InstallModelFont extends JCKModel
{

	
	private $_editor; 

	
	public function __construct($config = array())
	{
		
		
		if( defined('JLEGACY_CMS') )
		{
			$sql =  "SELECT id,params FROM #__plugins WHERE element = 'jckeditor' AND folder ='editors'" ;
		
		} else
		{
			$sql =  "SELECT extension_id as id, params FROM #__extensions WHERE element = 'jckeditor' AND folder ='editors'" ;
		
		}//end if
					
		$database =  JFactory::getDBO();   
		$database->setQuery( $sql );
		$result = $database->loadObject();
		$this->_editor = $result;
		
		
		
		parent::__construct($config);
	}
	
	
	private function _getParams()
	{
			
		static $registry = NULL;
			
		if(is_null($registry))
		{
			$registry = new JRegistry($this->_editor->params);
		}		
		return $registry;
	}
	

	
	
	public function getFontFamilyList()
	{
	 	$params = $this->_getParams();
		$default = $params->get('ftfamily','Automatic');
		
		$options = array(
						 JHTML::_('select.option', 'Arial', 'Arial'),
						 JHTML::_('select.option', 'Comic Sans MS', 'Comic Sans MS'),
						 JHTML::_('select.option', 'Courier', 'Courier'),
						 JHTML::_('select.option', 'Geneva', 'Geneva'),
						 JHTML::_('select.option', 'Helvetica', 'Helvetica'),
						 JHTML::_('select.option', 'sans-serif', 'sans-serif'),
						 JHTML::_('select.option', 'Tahoma', 'Tahoma'),
						 JHTML::_('select.option', 'Times New Roman', 'Times New Roman'),
						 JHTML::_('select.option', 'Trebuchet MS', 'Trebuchet MS'),
						 JHTML::_('select.option', 'Verdana', 'Verdana')
						 );
		
		$list = JHTML::_('select.genericlist',  $options, 'ftfamily', 'class="box combobox" size="1" data-value="'. $default  .'"');
		
		return $list;
	}
		
	public function getDefaultFontColor()
	{
		$params = $this->_getParams();
		$default = $params->get('ftcolor','Automatic');
		return $default;
	}
	
	public function getFontSizeList()
	{
		$params = $this->_getParams();
		$default = $params->get('ftsize','Automatic');
		
		$options = array(
					 JHTML::_('select.option', '8', '8'),
					 JHTML::_('select.option', '9', '9'),
					 JHTML::_('select.option', '10', '10'),
					 JHTML::_('select.option', '11', '11'),
					 JHTML::_('select.option', '12', '12'),
					 JHTML::_('select.option', '14', '14'),
					 JHTML::_('select.option', '16', '16'),
					 JHTML::_('select.option', '18', '18'),
					 JHTML::_('select.option', '20', '20'),
					 JHTML::_('select.option', '22', '22'),
					 JHTML::_('select.option', '24', '24'),
					 JHTML::_('select.option', '26', '26'),
					 JHTML::_('select.option', '28', '28'),
					 JHTML::_('select.option', '36', '36'),
					 JHTML::_('select.option', '48', '48'),
					 JHTML::_('select.option', '72', '72')
					 );
	
		$list = JHTML::_('select.genericlist',  $options, 'ftsize', 'class="box combobox" size="1" data-value="'. $default  .'"');
		
		return $list;
	}
	
	public function getDefaultBackgroundColor()
	{
		$params = $this->_getParams();
		$default = $params->get('bgcolor','#FFFFFF');
		return $default;
	}
	
	
	public function store()
	{
		$post = JRequest::get('post');
		
		foreach($post as $key=>$value)
		{
			if(strtolower(trim($value)) == 'automatic')
				$post[$key] = '';
		}
		if( defined('JLEGACY_CMS') )
			$table = JTable::getInstance('plugin');
		else
			$table = JTable::getInstance('extension');
		
		$registry = $this->_getParams();
		$registry->loadArray($post);
				
		$table->load($this->_editor->id);	
		$table->params	= $registry->toString();
		return $table->store();
	}

}
