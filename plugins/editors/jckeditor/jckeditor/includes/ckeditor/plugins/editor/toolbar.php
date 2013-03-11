<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');
jckimport('ckeditor.htmlwriter.javascript');


class plgEditorToolbar extends JPlugin 
{
		
  	function plgEditorToolbar(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	function beforeLoad(&$params)
	{
		//lets create JS object
		$javascript = new JCKJavascript();
		
		$toolbar =  $params->get('jck_toolbar','');

		$javascript->addScriptDeclaration(
			"editor.on( 'configLoaded', function(evt)
			{
				var editor = evt.editor;
				var toolbarName = '".$toolbar." ';
				if(!toolbarName)
					return;
				var toolbar =  editor.config.toolbar_".$toolbar.";
	           			
				var sortArray = []; 
				var element;
				var removes = editor.config.removePlugins.split(',');	
		    
				for(var i= 0; i < toolbar.length;i++)	
				{	
					element = toolbar[i]
				     
					if(element instanceof Array)
					{
						var buttons = [];
						 
						for(var j = 0; j < element.length;j++)
						{
							
							var button = element[j];
							
							if(!button) 
							  continue;
							
							var title = button.toLowerCase() ;
							if(title == 'flash')
								title = 'jflash';
								
							if(title == 'about')
								title = 'jabout';

								
							if(CKEDITOR.tools.indexOf(removes,title ) != -1)
								continue
							buttons.push(button);
						}
						element = buttons;
						if(element.length)
							sortArray.push(element);
					}
				}	
				
				toolbar = sortArray;
				
				//cleanup
				if( typeof toolbar[toolbar.length] == 'string')
					delete  toolbar[toolbar.length];
				
				if( typeof toolbar[0] == 'string')
					delete  toolbar[0];
						
				editor.config.toolbar_".$toolbar." = toolbar;
			});"	
		);
		
		return $javascript->toRaw();
		
	}

}