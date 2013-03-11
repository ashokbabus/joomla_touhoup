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

class JCKConfigHandlerWindowFeaturesList
{
	function getOptions($key,$value,$default,$node,$params)
	{
        $options = '';
		
        if(is_array($value))
		{  
            $value = implode(",",$value);
		} 
        elseif($value &&  preg_match('/^\[.*\]$/',$value))
        {
            
            $value = str_replace('\'','"',$value);
            $value = json_decode($value); 
            $value =  implode("",$value);
        }
  	   	$options .= "\"$key='".$value."'\"\"";   
     
		return $options;
	}
    
}