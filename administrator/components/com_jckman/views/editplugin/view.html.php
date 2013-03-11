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

class JCKManVieweditplugin extends JViewLegacy
{
	protected $canDo;
	protected $app;
	protected $user;
	protected $form;
	protected $item;
	protected $state;
	protected $params;

	function display( $tpl = null )
	{
		$this->canDo		= JCKHelper::getActions();
		$this->app			= JFactory::getApplication();
		$this->user			= JFactory::getUser();
		$this->state		= $this->get('State');
		$this->item			= $this->get('Item');
		$this->form			= $this->get('Form');
		$this->params		= $this->prepareForm($this->item);

		if(!$this->canDo->get('core.edit'))
		{
			$this->app->redirect( JRoute::_( 'index.php?option=com_jckman&view=list', false ), JText::_( 'COM_JCKMAN_PLUGIN_PERM_NO_EDIT' ), 'error' );
			return false;
		}//end if

		$this->form->bind($this->item);

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JCKHelper::error( implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$this->app->input->set('hidemainmenu', true);

		$bar 	= JToolBar::getInstance('toolbar');
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $this->user->get('id'));

		JToolBarHelper::title( JText::_( 'JCK Plugin' ) .':' . chr( 32 ) . JText::_($this->item->name), 'plugin.png' );

		if( $this->canDo->get('core.create') && !$checkedOut )
		{
			JToolBarHelper::apply( 'list.apply' );
			JToolBarHelper::save( 'list.save' );
		}//end if

    	JToolBarHelper::cancel( 'list.cancel', 'JTOOLBAR_CLOSE' );
    	//JToolBarHelper::help( $this->app->input->get( 'view' ), true );

		JCKHelper::addSubmenu( $this->app->input->get( 'view' ) );

		$this->sidebar = JHtmlSidebar::render();
	}//end function

	function prepareForm(&$item)
	{
        if($item->iscore)
           @$data = file_get_contents( JPATH_COMPONENT.DS.'editor'.DS.'plugins'.DS.$item->name.'.xml' );
        else
           @$data = file_get_contents( JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'plugins'.DS.$item->name.DS.$item->name.'.xml' );

		if($data )
		{
			$data = preg_replace( array('/\<params group="options">/i','/\<params>/i','/\<params(.*)\<\/params\>/is'), array('<params name="advanced">','<params name="basic">','<config><fields name="params"><fieldset$1</fieldset></fields></config>'), $data );
			$data = str_replace( array( '<install', '</install', '<params', '</params', '<param', '</param' ), array( '<form', '</form', '<fieldset','</fieldset', '<field', '</field' ), $data );

			// Re-style fields to J3.0
			// Can't just str_replace because fields might already have a class
			$xml 	= JCKHelper::getXML( $data, false );
			$nodes 	= $xml->xpath( '//field[@type="radio" or @type="resizeradio"]' );
			
			foreach( $nodes as $node )
			{
				$radio = 'btn-group';
				$class = ( (string)$node->attributes()->class ) ? (string)$node->attributes()->class . chr( 32 ) . $radio : $radio;
			
				if( $node->attributes()->class )
				{
					$node->attributes()->class = $class;
				}
				else
				{
					$node->addAttribute( 'class', $class );
				}
			}
			
			$data = $xml->asXML();
		} else
		{
			$data = '<install><form>dummy data</form></install>';
		}//end if

		JCKForm::addFieldPath(JPATH_COMPONENT . DS . 'models' . DS . 'fields');
		$form = JCKForm::getInstance( 'com_jckman.plugin', $data,array(),true,'//config'); 
		//$model 	= $this->getModel();	
		//$form 	= $model->getPluginForm($data); 

		//load plugins language file
		$lang		= JFactory::getLanguage();
		$lang->load('com_plugins', JPATH_ADMINISTRATOR, null, false, false);

		JPluginHelper::importPlugin('content');

		$dispatcher	= JDispatcher::getInstance();

		// Trigger the form preparation event.
		$jpara	= new JRegistry( $item->params );
		$data = $jpara->toArray();
		$results = $dispatcher->trigger('onContentPrepareForm', array($form, $data));

		$form->bind($data);

		return $form;
	}
}