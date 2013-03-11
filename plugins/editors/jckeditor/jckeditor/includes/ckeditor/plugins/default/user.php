<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

/**
 * @version 1.2	-	Corrected 1.6 issue
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.event.plugin');

class plgDefaultUser extends JPlugin 
{
		
  	function plgDefaultUser(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}
	
	//default method that is called
	function intialize(&$params) // Editor's params passed in
	{
		//check editor parameters
		$useUserFolder 	=  $params->get('useUserFolders',0);
		$userFolderType =  $params->get('userFolderType','username');
		
		
		if(!$useUserFolder)
			return;
		
		//Get user
		$user = JFactory::getUser();
		//Joomla does not have a function to determine if a user beloning
		//to a group is in this set.  As a result we have to do the work
		//ourselves.
		$groups			= $user->getAuthorisedGroups();
		$restirectTo 	= $params->get( 'displayFoldersTo', false );
		if( $groups && $restirectTo )
		{
			for( $n=0, $i=count($groups); $n<$i; $n++ )
			{
				if( in_array( $groups[$n], $restirectTo ) )
				{
					//Seems this user is able to view all folders.
					return;
				}//end if
			}//end for loop
		}//end if

		//Get user id
		if($userFolderType == 'username')
		{
			$id =  $user->username;
		}	
		else
		{ 	
			$id  = $user->id;	
		}

		//Now set media paths
		//Get current value set in DB
		$imagePath = $params->get('imagePath','images') . '/' . $id;
		$params->set('imagePath',$imagePath);
		
		
		$flashPath = $params->get('flashPath','flash')  . '/'. $id;
		$params->set('flashPath',$flashPath);
		
		
		//Now set media paths
		//Get current value set in DB
		$filePath = $params->get('filePath','files')  . '/' . $id;
		$params->set('filePath',$filePath);
		
	}
	
}