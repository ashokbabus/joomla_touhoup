<?php
/**
 * @version		$Id: cache.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Joomla! Page Cache Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.cache
 */
 
class plgSystemJCKtypography extends JPlugin
{

	var $_cache = null;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param	array	$config  An array that holds the plugin configuration
	 * @since	1.0
	 */

     function onAfterRoute()
     {

		$app = JFactory::getApplication();
		
		if ($app->isAdmin()) {
			return;
		}
		
		if(!file_exists(JPATH_PLUGINS.'/editors/jckeditor')) {
			return;
		}

		$doc = JFactory::getDocument();
		
		if($doc->getType() != 'html') {  //If not correct document type  exit
			return;
		}
		
		jimport('joomla.plugin.helper');
		
		$editor = 	JPluginHelper::getPlugin('editors','jckeditor');
		
		if(is_string($editor->params)) //always must do this check
		$params = @ new JRegistry($editor->params);
		else $params = 	$editor->params;
		
		
		if(!$params->get('jcktypography', false))
		{
			return; // nothing to do
		}
		
		if(!$params->get('jcktypographycontent', ''))
		{
			return; // nothing to do
		}
		
		$data = $doc->getHeadData();
		$stylesheet = array();
		$url = JURI::base(true).'/plugins/editors/jckeditor/typography/typography2.php';
		$stylesheet[$url]['mime'] = 'text/css';
		$stylesheet[$url]['media'] = null;
		$stylesheet[$url]['attribs'] = array();
		$data['styleSheets'] = $stylesheet + $data['styleSheets'];
		$doc->setHeadData($data);
     }
}
