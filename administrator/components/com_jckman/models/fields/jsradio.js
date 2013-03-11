/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

var JSRadioParam = function(){ this.constructor.apply(this, arguments); }
JSRadioParam.prototype = 
{
	constructor: function()
	{
		var boxes = document.getElements('.jck_radio');
		for ( var i=0; i < boxes.length; i++)
		{
			this.initialize(boxes[i]);
		}
	},

	initialize: function( el )
	{
		// Get paired textbox
		var parent	= el.getParent( 'div#options' );				// params parent
		var id		= el.get( 'id' ).replace( '_switcher', '' );	// textbox id from radio
		var option	= el.getElement( 'input:checked' );				// selected radio
		var text 	= parent.getElement( 'input#' + id );			// textbox

		// Successfully got element?
		if( text )
		{
			// Pre select
			if( option.value == 0 )
			{
				text.set( 'disabled', 'disabled' );
			}

			// Add switch event to inputs
			option.getParent().getChildren( 'input' ).each( function( e )
			{
				e.addEvent( 'click', function( ev )
				{
					this.switcher( e );
				}.bind( this ));
			}, this );
		}
	},

	switcher: function( el )
	{
		var parent	= el.getParent( 'div#options' );					// params parent
		var field	= el.getParent();									// get fieldset for ID
		var id		= field.get( 'id' ).replace( '_switcher', '' );		// textbox id from radio
		var text 	= parent.getElement( 'input#' + id );				// textbox
		var labels	= field.getChildren( 'label' );						// option labels
		var value	= '';

		// Successfully got element?
		if( text )
		{
			// Get active  option text
			labels.each( function( e )
			{
				if( e.get( 'for' ) == el.id )
				{
					value = e.innerHTML;
				}
			});

			// Activate or disable
			if( el.value == 0 )
			{
				text.set( 'disabled', 'disabled' );
				text.set( 'value', value );
			}
			else
			{
				text.removeAttribute( 'disabled' );
				
				var current = text.get( 'value' );
				
				if( current == '' || current == 'Default' )
				{
					text.set( 'value', value );
				}

				text.focus();
			}
		}
	}
}

document.radiolist = null
window.addEvent('domready',function()
{
  var radiolist = new JSRadioParam()
  document.radiolist = radiolist
});