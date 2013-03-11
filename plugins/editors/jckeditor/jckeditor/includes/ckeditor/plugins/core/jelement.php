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


class plgCoreJElement extends JPlugin 
{
		
  	function plgCoreJElement(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}
	

	//default method that is called
	function intialize(&$params) // Editor's params passed in
	{
		$javascript = new JCKJavascript();
		$javascript->addScriptDeclaration("
			(function()
			{
				 CKEDITOR.dom.element.prototype.setOpacity = function( opacity ) 
				 {
					if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 ) {
						opacity = Math.round( opacity * 100 );
						this.setStyle( 'filter', opacity >= 100 ? '' : 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')' );
					} else
						this.setStyle( 'opacity', opacity );
				}
			}
			)();");
		return $javascript->toRaw();
	}
	
}

		


