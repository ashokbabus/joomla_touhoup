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


class plgCoreEditor extends JPlugin 
{
		
  	function plgCoreEditor(& $subject, $config) 
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
				CKEDITOR.editor.replace = function( elementOrIdOrName, config )
				{
					var element = elementOrIdOrName;

					if ( typeof element != 'object' )
					{
						// Look for the element by id. We accept any kind of element here.
						element = document.getElementById( elementOrIdOrName );
						
						// Elements that should go into head are unacceptable (#6791). 
						 if ( element && element.tagName.toLowerCase() in {style:1,script:1,base:1,link:1,meta:1,title:1} ) 
									element = null; 
						
						// If not found, look for elements by name. In this case we accept only
						// textareas.
						if ( !element )
						{
							var i = 0,
								textareasByName	= document.getElementsByName( elementOrIdOrName );

							while ( ( element = textareasByName[ i++ ] ) && element.tagName.toLowerCase() != 'textarea' )
							{ /*jsl:pass*/ }
						}

						if ( !element )
							throw '[CKEDITOR.editor.replace] The element with id or name \"' + elementOrIdOrName + '\" was not found.';
					}

					// Do not replace the textarea right now, just hide it. The effective
					// replacement will be done by the _init function.
					element.style.visibility = 'hidden';

					// Create the editor instance.
					return new CKEDITOR.editor( config, element, CKEDITOR.ELEMENT_MODE_REPLACE );
				}
			}
			)();");
		return $javascript->toRaw();
	}
	
}

		


