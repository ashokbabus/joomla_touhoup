/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

var JSComboParam = function(){ this.constructor.apply(this, arguments); }
JSComboParam.prototype = 
{
	constructor: function()
	{
		var boxes = document.getElements('.jck_combo');
		for ( var i=0; i < boxes.length; i++)
		{
			this.initialize(boxes[i]);
		}
	},

	initialize: function( el )
	{
		// Get paired textbox
		var parent	= el.getParent( 'div#options' );			// params parent
		var id		= el.get( 'id' ).replace( 'Switcher', '' );	// textbox id from combo
		var text 	= parent.getElement( 'input#' + id );		// textbox
		var select	= el.getSiblings( 'div' );					// jQuery Selectbox

		// Successfully got element?
		if( text )
		{
			// Add switch event to inputs
			select.getElements( 'li' ).each( function( e )
			{console.log( e );
				e.addEvent( 'click', function( ev )
				{
					this.switcher( el );
				}.bind( this ));
			}, this );
		}
	},

	switcher: function( el )
	{
		var parent	= el.getParent( 'div#options' );					// params parent
		var id		= el.get( 'id' ).replace( 'Switcher', '' );			// textbox id from combo
		var text 	= parent.getElement( 'input#' + id );				// textbox
		var value	= el.getSelected().get( 'value' );					// selected combo

		// Successfully got element?
		if( text )
		{
			// Activate or disable
			if( value != 'c' )
			{
				text.set( 'value', value );
			}
			else
			{
				text.focus();
			}
		}
	}
}