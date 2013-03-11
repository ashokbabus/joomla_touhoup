<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @package		JCK
 * @subpackage	jckeditor
 * @since		1.0.1
 */
error_reporting(E_ERROR);

class plgEditorsJckeditorInstallerScript
{
	/**
	 * Post-flight extension installer method.
	 *
	 * This method runs after all other installation code.
	 *
	 * @param	$type
	 * @param	$parent
	 *
	 * @return	void
	 * @since	1.0.3
	 */
	function postflight($type, $parent)
	{
		// Display a move files and folders to parent.
		
			
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
			
		$srcBase = JPATH_PLUGINS.'/editors/jckeditor/jckeditor/'; 
		$dstBase = JPATH_PLUGINS.'/editors/jckeditor/';
		
		//get list of files and folders
		$files = JFolder::files($srcBase);
		$folders = JFolder::folders($srcBase);
		
		foreach($files as $file)
			JFile::copy($srcBase.$file,$dstBase.$file,null);
			
		foreach($files as $file)
			JFile::delete($srcBase.$file); //tidy up!
		
		//lets move htaccess file
		JFile::copy($srcBase.'.htaccess',$dstBase.'htaccess.txt', null);	
		JFile::delete($srcBase.'.htaccess'); //tidy up!
					
		foreach($folders as $folder)
		{
			if($folder == 'includes')
				continue;
				
			$manifest = $parent->getParent()->getManifest();	
			$attributes = $manifest->attributes();	
		    
		
			$method = ($attributes->method ? (string)$attributes->method : false); 
			
			if($method !='upgrade')
			{
				if(JFolder::exists($dstBase.$folder))
					JFolder::delete($dstBase.$folder);
			}
			JFolder::copy($srcBase.$folder,$dstBase.$folder,null, true);
		}
		
		foreach($folders as $folder)
		{
			if($folder == 'includes')
				continue;
			JFolder::delete($srcBase.$folder); //tidy up!	
				
		}
		
		$file = '_jckeditor.xml';
		JFile::delete($dstBase.$file); //remove all Joomla version install file 
		$this->_updateAdminToolsHtaccess();
	}
	
	function uninstall($parent) 
	{
		
		jimport('joomla.filesystem.folder');
		
		$mainframe = JFactory::getApplication();
		
		$db = JFactory::getDBO();
		
		
		$query = 'DELETE FROM `#__extensions`' .
		' WHERE folder = '.$db->Quote('system') .
		' AND element = '.$db->Quote('jcktypography');
		$db->setQuery( $query );
		if( !$db->query() ){
			$mainframe->enqueueMessage( JText::_('Unable to remove JCK Typography system plugin from database!') );
			return false;
		}
		
		$file =  JPATH_PLUGINS.'/system/jcktypography';
		
		if(JFolder::exists($file) && !JFolder::delete($file)) {
			$mainframe->enqueueMessage( JText::_('Unable to delete JCK Typography system plugin folder!') );
		}
		
	}
	
	function  _updateAdminToolsHtaccess()
	{
		// Define the files and folders to add to .htaccess Maker here:
		
		$base = 'plugins/editors/jckeditor/'; 
		
		$registry = null;
		
		jimport('joomla.filesystem.folder');
		
		$buffer = JFile::read(JPATH_ROOT.'/'.$base.'install/access/exceptions.ini');
		
		$chunks = explode(chr(13),trim($buffer));
	
		$folders =  array();
		$files = array();
		
		foreach($chunks as $chunk)
		{
			list($k,$v) = explode('=',$chunk);

			if($v == 'folder')
				$folders[] = $base.trim($k);
			elseif($k && $v)
				$files[] = $base.trim($k).'.'.trim($v);
		}
	
		$htmaker_additions = array(
			'folders'	=> $folders,
			'files'		=> $files
		);
	
		// DO NOT MODIFY BELOW THIS LINE

		// Is Admin Tools installed?
		if(!is_dir(JPATH_ADMINISTRATOR.'/components/com_admintools')) {
			return;
		}
		
		// Is it the Professional version?
		if(!is_file(JPATH_ADMINISTRATOR.'/components/com_admintools/models/htaccess.php') && 
		!is_file(JPATH_ADMINISTRATOR.'/components/com_admintools/models/htmaker.php') ) {
			return;
		}
	
		// Is Admin Tools enabled?
		$db = JFactory::getDbo();
		if(version_compare(JVERSION, '1.6.0', 'ge')) {
			$query = $db->getQuery(true)
				->select($db->qn('enabled'))
				->from($db->qn('#__extensions'))
				->where($db->qn('element').' = '.$db->q('com_admintools'))
				->where($db->qn('type').' = '.$db->q('component'));
			$db->setQuery($query);
		} else {
			$db->setQuery('SELECT `enabled` FROM `#__components` WHERE `link` = "option=com_admintools"');
		}
		$enabled = $db->loadResult();
		if(!$enabled) return;

		// Do we have a custom .htaccess file?
		$generateHtaccess = false;
		jimport('joomla.filesystem.file');
		if(JFile::exists(JPATH_ROOT.'/.htaccess'))
		{
			$htaccess = JFile::read(JPATH_ROOT.'/.htaccess');
			if($htaccess !== false) {
				$htaccess = explode("\n", $htaccess);
				if($htaccess[1] == '### Security Enhanced & Highly Optimized .htaccess File for Joomla!') {
					$generateHtaccess = true;
				}
			}
		}

		// Load the FoF library
		if(!defined('FOF_INCLUDED')) {
			include_once JPATH_LIBRARIES.'/fof/include.php';
		}

		// Load the .htaccess Maker configuration
		if(!class_exists('AdmintoolsModelStorage')) {
			include_once JPATH_ADMINISTRATOR.'/components/com_admintools/models/storage.php';
		}
		$model = FOFModel::getTmpInstance('Htmaker','AdmintoolsModel');
		$config = $model->loadConfiguration();

		if(is_string($config->exceptionfiles)) {
			$config->exceptionfiles = explode("\n", $config->exceptionfiles);
		}
		if(is_string($config->exceptiondirs)) {
			$config->exceptiondirs = explode("\n", $config->exceptiondirs);
		}

		// Initialise
		$madeChanges = false;

		// Add missing files
		if(!empty($htmaker_additions['files'])) {
			foreach($htmaker_additions['files'] as $f) {
				if(!in_array($f, $config->exceptionfiles)) {
					$config->exceptionfiles[] = $f;
					$madeChanges = true;
				}
			}
		}

		// Add missing folders
		if(!empty($htmaker_additions['folders'])) {
			foreach($htmaker_additions['folders'] as $f) {
				if(!in_array($f, $config->exceptiondirs)) {
					$config->exceptiondirs[] = $f;
					$madeChanges = true;
				}
			}
		}

		if($madeChanges) {
			// Save the configuration
			
			$customhead =  $config->custhead;
			if(!strpos($customhead,'pixlr.com'))
				$customhead .= "\nRewriteCond %{QUERY_STRING} image=http://[a-zA-Z0-9_]+\.pixlr.com
RewriteRule .* - [L]";
			
			$updates = array(
				'exceptionfiles' => implode("\n", $config->exceptionfiles),
				'exceptiondirs' => implode("\n", $config->exceptiondirs),
				'custhead'=> $customhead
			);

			$model->saveConfiguration($updates);
			if($generateHtaccess) {
				$model->writeHtaccess();
			}
		}
	}
}