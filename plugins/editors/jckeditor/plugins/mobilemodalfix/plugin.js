/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
CKEDITOR.plugins.add( 'mobilemodalfix',
{

	init : function( editor )
	{
		if(CKEDITOR.env.iOS)
		{
			var mainDocument = CKEDITOR.document,
			mainWindow = mainDocument.getWindow();
		
			editor.on('afterCommandExec', function(evt)
			{
				 var editor = evt.editor;
				
				if (evt.data.name == 'mobileimage')
				{
					var viewPaneSize = mainWindow.getViewPaneSize();	
					if(parseInt(viewPaneSize.width) <= 480)
					{
						 var box = document.getElementById("sbox-window");
						 var cover = document.getElementById("sbox-overlay");
						 setTimeout( function ()
						 {
							cover.style.display = "none";
							box.style.left = 0;
						},500);
					}
				}
			});
			
		var getX = function(obj) {
			var curtop = 0;
			if (obj.offsetParent) {	
				do {
					curtop += obj.offsetTop;	
				} while (obj = obj.offsetParent);
			}
			return curtop;
		}
			
			editor.on('dialogShow', function(evt)
			{
					var viewPaneSize = mainWindow.getViewPaneSize();	
					var width = parseInt(viewPaneSize.width);
					var top = getX(evt.editor.container.$);
					
					if(width  <= 480)
					{
						 var elem = evt.data._.element;
						 var element = elem.$.firstChild;
				
						elem = elem.$.nextSibling;
						if(elem) elem.style.display = 'none';
						
						element.style.position = 'absolute';
						element.style.left = "0px";
						element.style.top = top + "px";
		
					}	
			});
		}	
	}
});



