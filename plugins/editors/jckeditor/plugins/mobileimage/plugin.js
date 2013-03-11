/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/**
 * @imagemobile plugin.
 */
(function()
{

	var mobileImageCmd =
	{
		modes : { wysiwyg:1, source:0 },

		exec : function( editor )
		{
			var href = editor.config.baseHref +'administrator/index.php?option=com_media&view=images&tmpl=component&e_name=jform_articletext&asset=com_content&author=';
			
			SqueezeBox.open(null,
			{
				handler: 'iframe', 
				size: {x: 800, y: 500},
				url: href
			});
		}
	};
	
	var commandName = 'mobileimage';


	// Register a plugin named "save".
	CKEDITOR.plugins.add( 'mobileimage',
	{
		init : function( editor )
		{

			var command = editor.addCommand( commandName, mobileImageCmd );
			editor.ui.addButton( 'Mobileimage',
			{
				label : 'Image',
				command : commandName,
				icon: this.path + "icon.png"
			});
		}
	});
})();
