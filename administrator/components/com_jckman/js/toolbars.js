/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

function getSelections()
{
	return jQuery( '#jform_selections' );
}

function allselections()
{
	var e = getSelections();

	e.attr( 'disabled', false );
	e.find( 'option' ).attr( 'disabled', false );
	e.find( 'option' ).attr( 'selected', true );
	e.trigger( 'liszt:updated' );
}

function disableselections()
{
	var e = getSelections();

	e.attr( 'disabled', true );
	e.find( 'option' ).attr( 'disabled', true );
	e.find( 'option' ).attr( 'selected', false );
	e.trigger( 'liszt:updated' );
}

function enableselections()
{
	var e = getSelections();

	e.attr( 'disabled', false );
	e.find( 'option' ).attr( 'disabled', false );
	e.trigger( 'liszt:updated' );
}