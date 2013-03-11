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

class plgDefaultDragNDrop extends JPlugin 
{
		
  	function plgDefaultDragNDrop(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}
	
	//default method that is called
	function intialize(&$params) // Editor's params passed in
	{
		$dragndrop =  JRequest::getInt('dragndrop',0);

		if($dragndrop)
		{	
			//Now set image path
			//Get current value set in DB
			$uploadPath =  $params->get('imageDragndropPath','images');
			$params->set('imagePath',$uploadPath);
		}
	}
	
}