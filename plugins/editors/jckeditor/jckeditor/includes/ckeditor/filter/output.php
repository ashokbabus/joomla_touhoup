<?php 
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

class JCKOutput
{

 	static function cleanString($str)
  	{
	// remove any whitespace, and ensure all characters are alphanumeric
     $str = preg_replace(array('/\s+/','/\[/','/[^A-Za-z0-9_\-]/'), array('-','_',''), $str);
     // trim
     $str = trim($str);
     return $str;
    }
	
	static function fixId($id)
	{
		return JCKOutput::cleanString($id);
	}

}