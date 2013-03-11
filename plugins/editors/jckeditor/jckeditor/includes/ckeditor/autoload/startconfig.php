<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

class JCKStartConfig
{
	//set options to 1 for autoload overwise 0 to stop editor autoloading at atartup for component
	//otherwise set blank to tske the global setting
	var $com_content = 1;
	var $com_categories = 1;
	var $com_sections = 1;
}