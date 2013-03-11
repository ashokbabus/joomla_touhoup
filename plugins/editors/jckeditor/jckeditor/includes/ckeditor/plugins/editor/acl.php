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

error_reporting(E_ERROR); 

jimport('joomla.event.plugin');
jimport('joomla.html.parameter');
jckimport('ckeditor.htmlwriter.javascript');

class plgEditorACL extends JPlugin 
{
		
  	function plgEditorACL(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function beforeLoad(&$params)
	{
			
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		
	
		if($user->authorise('core.admin'))
			return;
		
		$query = 'SELECT id,name,acl,parentid FROM #__jckplugins WHERE published = 1';
	
		$db->setQuery( $query );
		$plugins = $db->loadObjectList();
		
		if (!is_array($plugins)) {
			JCKHelper::error( $db->getErrorMsg() );
		}
		
		if(empty($plugins))
			return;
		
		$groups	= $user->getAuthorisedGroups();
		
		
		$js = '';
		
		$deniedPlugins = array();
		$removePlugins = array();
				
		foreach($plugins as $plugin)
		{
			
			if(is_null($plugin->acl))
				continue;
			
			
	
			$acl = json_decode($plugin->acl);
			
			$allow = true;
			
			
	
			if(empty($acl))
			{
				$allow = false;
				$deniedPlugins[] = $plugin->id;
				$removePlugins[] = $plugin->name;
			}	
			else
			{
				
				if( $groups )
				{
					$allow = false;
					for( $n=0, $i=count($groups); $n<$i; $n++ )
					{
						if( in_array( $groups[$n], $acl) )
						{
							$allow = true;
							break;
						}//end if
								
					}//end for loop
					if(!$allow)
					{
						$deniedPlugins[] = $plugin->id;
						$removePlugins[] = $plugin->name;
					}
				}//end if
				
				// check to see if parent plugin access view level is denied. If is then parent settings override
				if($allow && in_array( $plugin->parentid, $deniedPlugins))
				{
					$deniedPlugins[] = $plugin->id;
					$removePlugins[] = $plugin->name;
				}
			}

		}
				
		//var_dump($removePlugins);
	
		if(empty($removePlugins))
			return;
				
		//lets create JS object
		$javascript = new JCKJavascript();
		
		$plugs = implode(',',$removePlugins);
		
		$javascript->addScriptDeclaration(
			"editor.on( 'configLoaded', function()
			{
				if(editor.config.removePlugins) 
					editor.config.removePlugins += ',".$plugs."';
				else 	
					editor.config.removePlugins += '".$plugs."';
			});"	
		);
				
		return $javascript->toRaw();

	}

	

}