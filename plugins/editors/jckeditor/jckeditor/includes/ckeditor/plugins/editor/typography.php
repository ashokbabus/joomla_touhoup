<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or diefine( 'Restricted access' );

jimport('joomla.event.plugin');
jckimport('ckeditor.htmlwriter.javascript');


class plgEditorTypography extends JPlugin 
{
		
  	public function plgEditorTypography(& $subject, $config) 
	{
		parent::__construct($subject, $config);
	}

	public function beforeLoad(&$params)
	{
		
		//lets create JS object
		$javascript = new JCKJavascript();
				
		//lets create JS object
		$javascript = new JCKJavascript();
		

				
		$css = $params->get('jcktypography',false);
		$bgcolor	= 	$params->get( 'bgcolor','#ffffff');
		
			
		if($css)
		{
			$stylesheet =  str_replace("administrator/","",JURI::base()) . "plugins/editors/jckeditor/typography/typography.php";
			$javascript->addScriptDeclaration(
				"editor.on( 'configLoaded', function()
				{
					if(editor.config.contentsCss instanceof Array)
						editor.config.contentsCss.unshift('".$stylesheet."');
					else if(editor.config.contentsCss)	
						editor.config.contentsCss = ['".$stylesheet."',editor.config.contentsCss];
					else
						editor.config.contentsCss = ['".$stylesheet."'];	
					
					if(editor.config.stylesCss instanceof Array)
						editor.config.stylesCss.unshift('".$stylesheet."');
					else if(editor.config.stylesCss)	
							editor.config.stylesCss = ['".$stylesheet."',editor.config.stylesCss];
					else
						editor.config.stylesCss = '".$stylesheet."';	
					});"	
			);
		}
		$mainframe = JFactory::getApplication();
		
		$path_root = '../';
		
		if($mainframe->isSite())
			$path_root = '';
				
		$stylesheet = $this->_getStylesPath($params, $path_root);

		$javascript->addScriptDeclaration(
			"editor.on( 'configLoaded', function()
			{
				if(editor.config.stylesCss instanceof Array)
					editor.config.stylesCss.push('".$stylesheet."');
				else	if(editor.config.stylesCss)
					editor.config.stylesCss = ['".$stylesheet."',editor.config.stylesCss];
				else
					editor.config.stylesCss = ['".$stylesheet."'];	
				editor.config.stylesCss.push('body {  background: ". $bgcolor . " none; }');	
				
				if(CKEDITOR.tools.indexOf( editor.config.contentsCss,  '".$stylesheet."') == -1)
				{	
					editor.config.contentsCss.push('".$stylesheet."');
				}
					
				if(editor.config.extraPlugins)
					editor.config.extraPlugins += ',cmsstylesheet';
				else 	
					editor.config.extraPlugins += 'cmsstylesheet';	
					
			});"	
		);

		return $javascript->toRaw();
	}

	private function _getStylesPath($params,$path_root )
	{
	
		//Get parameter options for template CSS
		$content_css			=	$params->get( 'styles_css', 1 );
		
		$content_css_custom	=	$params->get( 'styles_css_custom', '' );
	
		
		$db = JFactory::getDBO();
		
		$query	= $db->getQuery(true);
		$query->select('template')
				->from('#__template_styles')
				->where('client_id=0 AND home=1');

		$db->setQuery( $query );
		$template = $db->loadResult();

		//For some reason the Beez template are using General.css instead
		//of template.css, and the template.css file is setup to fail?!
		//This code checks to see what template we are using and switch
		//to General.css to solve the issue.
		//
		//By Mark Smeed - For 1.6 ONLY
		if( in_array( $template, array( 'beez_20', 'beez5' , 'beez3') ) && ( $content_css == 1 ) )
		{
			$content_css = 0;
			$content_css_custom = 'templates/'.$template.'/css/general.css';
		}//end if
		
		if ( $content_css) 
		{
		
			if( is_file( JPATH_SITE . '/templates/'.$template.'/css/template.css' ) )
			{
				$content_css = 'templates/'.$template.'/css/template.css';
				
			} 
			else if( is_file( JPATH_SITE . '/templates/'.$template.'/css/template.css.php' ) )
			{
			
			
				$content_css = 'templates/'.$template.'/css/JFCKeditor.css.php'; 
			  
				if(!is_file( JPATH_SITE . '/templates/'.$template.'/css/JFCKeditor.css.php') ||  
					filemtime(JPATH_SITE . '/templates/'.$template.'/css/template.css.php') > 
					filemtime(JPATH_SITE . '/templates/'.$template.'/css/JFCKeditor.css.php') ) 
				{
				   
      
					 $file_content = file_get_contents('../templates/'.$template.'/css/template.css.php');
					  
					 $file_content  =  preg_replace_callback("/(.*?)(@?ob_start\('?\"?ob_gzhandler\"?'?\))(.*)/",
					   create_function(
							'$matches',
							'return ($matches[1]) .\';\';'
							
						),$file_content);
					 
					 
					  $file_content = preg_replace("/(.*define\().*DIRECTORY_SEPARATOR.*(;?)/",'',$file_content);
										 
	   
					 $file_content =
					 
					 '<'. '?' . 'php' . ' function getYooThemeCSS() { ' . '?' . '>' . $file_content . '<'. '?' . 'php' .  ' } ' . '?' . '>';
					  
								  
					$fout = fopen($this->_path_root . $content_css,"w");
					fwrite($fout,$file_content);
					fclose($fout);
				}
				
				include($this->_path_root . $content_css);
				
				$content_css = 'templates/'.$template.'/css/JFCKeditor.css'; 
				
				ob_start();
								
				getYooThemeCSS();
											
				$file_content =  ob_get_contents(); 
														
				ob_end_clean();
													
				$fout = fopen($this->_path_root . $content_css,"w");
				fwrite($fout,$file_content);
				fclose($fout);
			}
			else
			{
				$content_css = 'plugins/editors/jckeditor/contents.css';
			}//end if 
		}
		else 
		{
			if ( $content_css_custom ) 
			{
               
			  	$hasRoot = strpos(' ' . strtolower($content_css_custom),strtolower(JPATH_SITE));
				$file_path = ($hasRoot ? '' : JPATH_SITE) .  ($hasRoot || substr($content_css_custom,0,1) == DS  ? '' : DS) .$content_css_custom;
           				
				if( is_file(  $file_path) )
				{
					$content_css =  $file_path;
					$content_css = str_replace(strtolower(JPATH_SITE) . DS,'',strtolower($content_css_custom));
				}
			} 
			else 
			{
				$content_css = 'plugins/editors/jckeditor/contents.css';
			}//end if $content_css_custom
			/*write to xml file and read from css asnd store this file under editors*/
							 
		}//end if $content_css || $editor_css
		$this->_content_css = $path_root .$content_css;
		$content_css =   JURI::root() . $content_css; 
	 	$content_css =   str_replace(DS,'/',$content_css); 

		return $content_css;
	}	
}