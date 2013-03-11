/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
CKEDITOR.plugins.add("forcesimpleamp",{init:function(a){},afterInit:function(a){var b=a.dataProcessor,c=b&&b.htmlFilter;if(c){c.addRules({elements:{a:function(b){if(b.attributes._cke_saved_href){var c=b.attributes._cke_saved_href;if(a.config.forcesimpleAmpersand&&c.test(/&/)){c=c.replace(/&/g,"&");b.attributes._cke_saved_href=c}}return b}},text:function(b){if(a.config.forcesimpleAmpersand&&b.test(/&/))b=b.replace(/&/g,"&");return b}})}}})