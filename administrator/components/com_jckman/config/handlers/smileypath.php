<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_PLATFORM') or die;

class JCKConfigHandlerSmileyPath 
{
	function getOptions($key,$value,$default,$node,$params)
	{
		$options = '';
				  
		if($value)
			$value = str_replace('/administrator','',JURI::base(true)).'/'.$value;		  
				  
	   	$options .= "\"$key='".$value."'\",";   
		
		return $options;
	}
}