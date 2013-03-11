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
// wrapper for com_installer controller
$language = JFactory::getLanguage();		
$language->load( 'com_installer', JPATH_ADMINISTRATOR );
require_once( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_installer' .DS. 'controller.php' );
require_once( JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_installer' .DS. 'controllers'. DS. 'install.php' );
