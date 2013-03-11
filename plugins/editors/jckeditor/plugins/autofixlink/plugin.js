/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
CKEDITOR.plugins.add("autofixlink",{init:function(a){},afterInit:function(a){var b=a.dataProcessor,c=b&&b.htmlFilter;if(c){c.addRules({elements:{a:function(a){if(a.attributes._cke_saved_href){var b=a.attributes._cke_saved_href;if(b.indexOf("www.")!=-1&&!b.match(/^http/)){a.attributes._cke_saved_href="http://"+b}}return a}}})}}})