/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @imagemobile plugin.
 */
(function()
{
	// Register a plugin named "save".
	CKEDITOR.plugins.add( 'mobileviewport',
	{
		init : function( editor )
		{
	
			var mainDocument = CKEDITOR.document,
			mainWindow = mainDocument.getWindow();
			
			var viewPaneSize = mainWindow.getViewPaneSize();
						
			// Saved resize handler function.
			function resizeHandler()
			{
				var viewPaneSize = mainWindow.getViewPaneSize();
				if(CKEDITOR.env.iOS && parseInt(viewPaneSize.width) <= 568)
						editor.resize( viewPaneSize.width-45, viewPaneSize.height, null, true );
				else if(CKEDITOR.env.iOS &&  viewPaneSize.width == 768)
				{
					editor.resize( viewPaneSize.width-150, viewPaneSize.height, null, true );
				}
				else if (CKEDITOR.env.iOS &&  viewPaneSize.width == 1024)				
				{
					editor.resize( viewPaneSize.width-210, viewPaneSize.height, null, true );
				}
			}
			
			
			// Add event handlers for resizing.
			mainWindow.on( 'resize', resizeHandler );
			
			editor.on('resize', function(evt)
			{
				//webkit does not redraw iframe correctly when editor's width is <= 320px 
				var viewPaneSize = mainWindow.getViewPaneSize();
				if (CKEDITOR.env.iOS && parseInt(viewPaneSize.width) <= 320) 
				{									
					var iframe = document.getElementById('cke_contents_' + editor.name).firstChild;
					iframe.style.display = 'none';
					iframe.style.display = 'block';
				}
				
			});
		
	
		
			/*
			var currentWidth = 0;
			
			window.addDomReadyEvent.add(function() 
			{
				if(CKEDITOR.env.iOS)
				{
					var doc = CKEDITOR.document;
					 var body =    doc.getBody();
										
					function rerorient()
					{
						 if (window.orientation % 180 == 0)
						 {
							body.setStyles(
							{
								"-webkit-transform-origin" : "",
								"-webkit-transform" : ""
							});
						}
						else
						{
							 if (window.orientation > 0)
							 {
								body.setStyles(
								{
									"-webkit-transform-origin" : "200px 190px",
									"-webkit-transform" : "rotate(-90deg)"
								});
							 }
							 else
							 {
								body.setStyles(
								{
									"-webkit-transform-origin" : "280px 190px",
									"-webkit-transform" : "rotate(90deg)"
								});
							 }
						
						}
					}
					 window.onorientationchange = rerorient;
					 rerorient();
				}
			});*/
		}
	});
})();
