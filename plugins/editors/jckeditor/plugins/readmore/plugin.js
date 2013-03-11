/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
CKEDITOR.plugins.add("readmore",{init:function(a){a.addCss("#system-readmore"+"{"+"background-image: url("+CKEDITOR.getUrl(this.path+"images/readmore.gif")+");"+"background-position: center center;"+"background-repeat: no-repeat;"+"clear: both;"+"display: block;"+"float: none;"+"width: 100%;"+"border-top: #999999 1px dotted;"+"border-bottom: #999999 1px dotted;"+"height: 7px;"+"}"+"#system-readmore"+"{"+"background-color: #E6F0F8;"+"border: #0B55C4 1px dotted;"+"}")}})