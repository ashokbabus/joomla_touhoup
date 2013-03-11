CKEDITOR.plugins.add( 'cmsstylesheet',
{
     beforeInit : function( editor )
   {
      // Override core API here.
      var old = CKEDITOR.ui.prototype.addRichCombo;
      
      CKEDITOR.ui.prototype.addRichCombo = function(name, definition)
      {
		definition.panel.css = editor.skin.editor.css.concat( editor.config.stylesCss );
		old.apply( this, [name, definition] );
      };
   },
    init : function( editor )
   {
      // Leave it emtpy for now...
   }
} );

