<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');


class plgFilebrowserAllVideos extends JPlugin 
{
		
  	function plgFilebrowserAllVideos(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function beforeSetFilePath(&$params)
	{
		
		$plugin = JPluginHelper::getPlugin('content','jw_allvideos');
		if(empty($plugin) && !isset($plugin->params))
			return;
		
		$avParams =  new JRegistry($plugin->params);
		
		$avBasePath =  $avParams->get('afolder','images/stories/audio');
		
		jckimport('ckeditor.user.user');
		$user =& JCKUser::getInstance();
		$mediatype = $user->mediatype;
		if($mediatype == 'video')
			$avBasePath =  $avParams->get('vfolder','images/stories/video');
		//now set filepath 	
		$params->set('filePath',$avBasePath);		
	}

}