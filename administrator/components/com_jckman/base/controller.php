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

jckimport('event.observable.editor');

class JCKController extends JControllerLegacy
{
	/**
	 * Custom Constructor
	 */
	private   $editor_obervable;
	protected  $event_args;

	public function __construct( $default = array())
	{
		parent::__construct( $default );

		$app = JFactory::getApplication();
		$this->_event_args = null;
		$name = $app->input->get( 'controller', '');
		
		if(!$name) 
			$name = $app->input->get( 'view', $this->getName() );

		$eventListenerFile = JPATH_COMPONENT .DS . 'event' . DS . $name . '.php';

		jimport('joomla.filesystem.file');

		if(JFile::exists($eventListenerFile))
		{
			require_once($eventListenerFile);			
			$this->editor_obervable = new JCKEditorObservable($name);
        }
		else
		{
			JCKHelper::error('No Event listener found for '. $name .' controller'); 
		}  

		//load style sheet
		$document = JFactory::getDocument();
		$document->addStyleSheet( JCK_COMPONENT . '/css/header.css', 'text/css' );
	}

	public function execute( $task )
	{
		parent::execute( $task );
		
		//if error just return
		//if(JError::getError())
		//	return;
		//fire event to update editor
		$this->updateEditor($this->getTask(),$this->event_args);
	}

	private function updateEditor($event,$args = array())
	{
		if(isset($this->editor_obervable))
		{
			$this->editor_obervable->update( 'on' . JString::ucfirst($event),$args);
		}
	}
}