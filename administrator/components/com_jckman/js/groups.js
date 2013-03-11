/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

function getGroups()
{
	return jQuery( '#jform_groups' );
}

function allgroups()
{
	var e = getGroups();

	e.attr( 'disabled', false );
	e.find( 'option' ).attr( 'disabled', false );
	e.find( 'option' ).attr( 'selected', true );
	e.trigger( 'liszt:updated' );
}

function disablegroups()
{
	var e = getGroups();

	e.attr( 'disabled', true );
	e.find( 'option' ).attr( 'disabled', true );
	e.find( 'option' ).attr( 'selected', false );
	e.trigger( 'liszt:updated' );
}

function enablegroups()
{
	var e = getGroups();

	e.attr( 'disabled', false );
	e.find( 'option' ).attr( 'disabled', false );
	e.trigger( 'liszt:updated' );
}