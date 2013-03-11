<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
include('../../../includes.php');

defined( '_JEXEC' ) or die( 'Restricted access' );


$app = Jfactory::getApplication('site'); // A necessary step

$user = JFactory::getUser();
$color = JRequest::getVar('color');
$user->setParam('jckuicolor','#'.$color);
$user->save(true);
?>