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
// wrapper for com_installer controller
$language = JFactory::getLanguage();		
$language->load( 'com_installer', JPATH_ADMINISTRATOR );

jimport('joomla.client.helper');

class JCKManControllerImport extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		
		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName		= JRequest::getCmd('view', 'import');
		$vFormat	= $document->getType();
		$lName		= JRequest::getCmd('layout', 'default');

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat)) {
			$ftp	= JClientHelper::setCredentialsFromRequest('ftp');
			$view->assignRef('ftp', $ftp);

			// Get the model for the view.
			$model = $this->getModel($vName);

			// Push the model into the view (as default).
			$view->setModel($model, true);
			$view->setLayout($lName);

			// Push document object into the view.
			$view->assignRef('document', $document);
			$view->display();
		}

		return $this;
	}
	
	public function import()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('import');
		if ($model->import()) {
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();
		}
		//now updated editor
		jckimport( 'event.observable.editor' );
		$obs	= new JCKEditorObservable( 'cpanel' );
		$handle = $obs->getEventHandler();
		$handle->onSync();	
		$this->display();
	}
	
	
	
}
