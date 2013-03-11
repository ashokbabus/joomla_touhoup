/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
CKEDITOR.plugins.add('jhtmlencode',   
{    

     beforeInit:  function(editor)
	 {
		var element = editor.element;
		
		if(element.is('textarea'))
		{			  
			var data = element.getText();
			
			var div = new CKEDITOR.dom.element( 'div' );
			div.setHtml( data );
			data = div.getHtml();
			data = CKEDITOR.tools.htmlEncode(data);
			element.setHtml(data);
			
			delete div;
		}
	 },
	 	 
	 init:function(editor) 
	 {
		//Nothing to do	 
	 }
	  
});