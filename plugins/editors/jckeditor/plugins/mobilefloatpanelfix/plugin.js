/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
CKEDITOR.plugins.add( 'mobilefloatpanelfix',
{
	requires : [ 'floatpanel' ]
});

(function ()
{
	CKEDITOR.ui.floatPanel.prototype.allowBlur = function(allow)
	{
		var panel = this._.panel;
		if(!CKEDITOR.env.iOS)  // disalble for iPhone and iPad
		{	
			if ( allow != undefined )
					panel.allowBlur = allow;

			return panel.allowBlur;
		}
		return false;
	}
	
		
	var oldfunc = CKEDITOR.ui.floatPanel.prototype.showBlock;
	
	 CKEDITOR.ui.floatPanel.prototype.showBlock = function( name, offsetParent, corner, offsetX, offsetY )
	 {
		if(CKEDITOR.env.iOS)  // add touch lisneners only for iPhone and iPad 
		{
			var doc = new CKEDITOR.dom.document( this._.iframe.$.contentWindow.document );
				
			var body = doc.getBody();
				
			var par = this._.iframe.getParent();
			var par = par.$;
			par.setAttribute('id',"scroller");
			win = this._.iframe.$.contentWindow;
		
			this._.iframe.on('load',function () 
			{
				var startY = 0;
				var startX = 0;
				var b = body.$;
				b.addEventListener('touchstart', function (evt) {
					startY = evt.targetTouches[0].pageY;
					startX = evt.targetTouches[0].pageX;
				});
				b.addEventListener('touchmove', function (evt) {
					evt.preventDefault();
					var posy = evt.targetTouches[0].pageY;
					var h = par;
					var sty = h.scrollTop;

					var posx = evt.targetTouches[0].pageX;
					var stx = h.scrollLeft;
					h.scrollTop = sty - (posy - startY);
					h.scrollLeft = stx - (posx - startX);
					startY = posy;
					startX = posx;
				});
			});
		}
		oldfunc.apply(this, arguments );
	}
	
})();