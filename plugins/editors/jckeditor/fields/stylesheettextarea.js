/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

var StylesheetTextareaParam = function() { this.constructor.apply(this, arguments);}

StylesheetTextareaParam.prototype = {

	constructor: function()
	{
		
		var boxes = document.getElements('.typography');
		for ( var i=0; i < boxes.length; i++) {
			if (boxes[i].tagName == 'TEXTAREA') {
				this.initialize(boxes[i]);
			}
		}
	},
 
	initialize: function(element)
	{

		buttonElement = document.createElement("input");
		
		breakElement = document.createElement("br");

		buttonElement.setAttribute("type","button");
		buttonElement.setAttribute("value","Expand View");
		
		element.parentNode.insertBefore(breakElement, element.nextSibling);
		breakElement.parentNode.insertBefore(buttonElement, element.breakElement);

		// Add modal click event

		buttonElement.addEvent("click",function()
		{
			SqueezeBox.fromElement(buttonElement,
			{
				url:'../plugins/editors/jckeditor/fields/modals/typography.php',
				handler: 'iframe',
				size: {x:640, y:480}
			});
		});	
		
		buttonElement.style.marginTop = "5px";
		element.style.overflow = "auto";


	}

}

Window.onDomReady(function(){
  var stylesheetTextareaParam = new StylesheetTextareaParam();
});
