/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
CKEDITOR.plugins.add( 'paneloverride',
{
	init: function(editor)
	{
		CKEDITOR.ui.panel.prototype.getHolderElement = function()
		{
			var holder = this._.holder;

			if ( !holder )
			{
				if ( this.forceIFrame || this.css.length )
				{
					var iframe = this.document.getById( this.id + '_frame' ),
						parentDiv = iframe.getParent(),
						dir = parentDiv.getAttribute( 'dir' ),
						className = parentDiv.getParent().getAttribute( 'class' ),
						langCode = parentDiv.getParent().getAttribute( 'lang' ),
						doc = iframe.getFrameDocument();
					// Initialize the IFRAME document body.
					doc.$.open();

					// Support for custom document.domain in IE.
					if ( CKEDITOR.env.isCustomDomain() )
						doc.$.domain = document.domain;

					var onLoad = CKEDITOR.tools.addFunction( CKEDITOR.tools.bind( function( ev )
						{
							this.isLoaded = true;
							if ( this.onLoad )
								this.onLoad();
						}, this ) );

						
					var css = [];

					css.push("background: "+ editor.config.bgcolor + " none"); 
					
					if(editor.config.ftcolor)
						css.push("color: "+ editor.config.ftcolor); 
						
					if(editor.config.ftfamily)
						css.push("font-family: "+ editor.config.ftfamily); 	
						
					if(editor.config.ftsize)
						css.push(" font-size: "+ CKEDITOR.tools.cssLength(editor.config.ftsize)); 		
						
					doc.$.write(
						'<!DOCTYPE html>' +
						'<html dir="' + dir + '" class="' + className + '_container" lang="' + langCode + '">' +
							'<head>' +
								'<style>.' + className + '_container{visibility:hidden}</style>' +
							'</head>' +
							'<body class="cke_' + dir + ' cke_panel_frame ' + CKEDITOR.env.cssClass + '" style="margin:0;padding:0;' + css.join(";") + '"' +
							' onload="( window.CKEDITOR || window.parent.CKEDITOR ).tools.callFunction(' + onLoad + ');"></body>' +
							// It looks strange, but for FF2, the styles must go
							// after <body>, so it (body) becames immediatelly
							// available. (#3031)
							CKEDITOR.tools.buildStyleHtml( this.css ) +
						'<\/html>' );
					doc.$.close();

					var win = doc.getWindow();

					// Register the CKEDITOR global.
					win.$.CKEDITOR = CKEDITOR;

					// Arrow keys for scrolling is only preventable with 'keypress' event in Opera (#4534).
					doc.on( 'key' + ( CKEDITOR.env.opera? 'press':'down' ), function( evt )
						{
							var keystroke = evt.data.getKeystroke(),
								dir = this.document.getById( this.id ).getAttribute( 'dir' );

							// Delegate key processing to block.
							if ( this._.onKeyDown && this._.onKeyDown( keystroke ) === false )
							{
								evt.data.preventDefault();
								return;
							}

							// ESC/ARROW-LEFT(ltr) OR ARROW-RIGHT(rtl)
							if ( keystroke == 27 || keystroke == ( dir == 'rtl' ? 39 : 37 ) )
							{
								if ( this.onEscape && this.onEscape( keystroke ) === false )
									evt.data.preventDefault();
							}
						},
						this );

					holder = doc.getBody();
					holder.unselectable();
				}
				else
					holder = this.document.getById( this.id );

				this._.holder = holder;
			}

			return holder;
		}
	}	
});


