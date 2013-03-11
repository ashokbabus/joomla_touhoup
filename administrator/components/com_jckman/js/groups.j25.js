/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// File not in use

function getGroups()
{
	return document.getElementById('jform_groups');
}

function allgroups()
{
	var e = getGroups();
		e.disabled = false;
	var i = 0;
	var n = e.options.length;
	for (i = 0; i < n; i++) {
		e.options[i].disabled = false;
		e.options[i].selected = true;
	}
}

function disablegroups()
{
	var e = getGroups();
		e.disabled = true;
	var i = 0;
	var n = e.options.length;
	for (i = 0; i < n; i++) {
		e.options[i].disabled = true;
		e.options[i].selected = false;
	}
}

function enablegroups()
{
	var e = getGroups();
		e.disabled = false;
	var i = 0;
	var n = e.options.length;
	for (i = 0; i < n; i++) {
		e.options[i].disabled = false;
	}
}