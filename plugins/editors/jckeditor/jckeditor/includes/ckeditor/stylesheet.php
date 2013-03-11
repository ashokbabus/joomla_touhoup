<?php

/*------------------------------------------------------------------------
# Copyright (C) 2005-2010 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

class JCKStylesheet 
{

	var $_nam;
	var $_elem;
	var $_prop;
	var $_dir = '';
	var $_path_root;
	var $_default_beez_template = '';    
	var $_params;
	var $_type = ""; 
	
	
	function __construct($params, $path_root = '')
	{
		$this->_nam = array();
		$this->_elem = array();
		$this->_prop = array();
		$this->_path_root = $path_root;
		$this->_params = $params; 
		
	}
	
	static function & getInstance(& $params ,$path_root = null )
	{
		static $instance;
		
		if(is_null($path_root))
		{
		  if(!empty($instance))
			return reset($instance); //return first element
		  else
			$path_root = ''; //set path to frontend as default
		}
		
		$base = (!$path_root ? 'site' : $path_root);
		
		if(empty($instance[$base]))
		{
			$instance[$base] = new JCKStylesheet($params, $path_root);
		}
		return $instance[$base];
	}
	
	function getJSObject()
 	{
		$txt_filename = $this->getPath($errors,'styles');
		$txt_filename = $this->_path_root .$txt_filename;
		
		$js_str="[";
	
			
		$file = file_get_contents($txt_filename);
		
		$this->_dir = dirname($txt_filename);
		
		$this->_parse($file);
		
		if(count($this->_nam))
		{
			$count = 0;
			$max = count($this->_nam);
			foreach($this->_nam as $k=>$val)
			{
			
			   $endline = '';
			  
			   if($count >= 0 && $count < $max -1)
			   {
			   	 $endline = ',';
			   }
			
				$js_str.= "{" .
							"\nname : '$val'," .
							"\nelement : '". $this->_elem[$k] ."', ".
							"\nattributes : ".
							"{".
							"\n	'". $this->_prop[$k] ."' : '". $val ."'".
							"\n}".
						"}";	
				$js_str.= $endline;
				$count++;
		
			}//end for loop
		}//end count
		$js_str.="\n]"; 
		
		
		return $js_str;
	
 	}//end function	
	
		
	function _parse($file)
	{
	
		//Get JCK additional styles
				
		//Get editor params
		$plugin = JPluginHelper::getPlugin('editors','jckeditor');
		if(is_string($plugin->params)) //always must do this check
			$params = @ new JRegistry($plugin->params);
		else $params = 	$plugin->params;
		
		if(!$params->get('styles_css',true))
			$this->_default_beez_template = '';
		
		$params->set('default_beez_template',$this->_default_beez_template);
		
		jckimport('ckeditor.plugins.helper');
		
		//import plugins
		JCKPluginsHelper::storePlugins('stylesheet');
		JCKPluginsHelper::importPlugin('stylesheet');
		
		$dispatcher =  JDispatcher::getInstance();
        
		$results  =  $dispatcher->trigger('load',array( &$params));
		
		$results = array_reverse($results);
		
		for($i = 0; $i < count($results);$i++)
		{
			if($results[$i])
				$this->_readCSS($results[$i]);
		}   
		 
		 
	 
		preg_match_all('/^\s*(?:[a-z0-9\s\b]*)@import\s*(?:url\()?(?:"|\')?([^"\'\)]+)(?:"|\')?\)?;/im',$file,$fmatches,PREG_SET_ORDER);
		
		foreach($fmatches as $fmatch)
		{
		
			$oldumask = umask(0);
		 	@chmod( $fmatch[1], 0666);
		 	umask( $oldumask );
			if(!strpos($fmatch[1],'://'))
		 		$content = file_get_contents($this->_dir ."/" .$fmatch[1]);
			else
				$content = file_get_contents($fmatch[1]);
			$this->_parse($content);
		}// foreach fmatches
		$this->_readCSS($file);
		
	}//end function	


	
	function _readCSS($file)
	{
	
	
		$allowed_elements = array('\.','#','div','span','hr','table','td','tr','img','input','textarea');
  	
		$elem_list = implode('|',$allowed_elements ); 
		$allowed_elements[0] = '.';
		array_unshift($allowed_elements, "^");
				
		preg_match_all("/\s*(" . $elem_list  . ")?(\.|#)?([a-z0-9\.#_\*\-\n\r\t, ]*)(?:\s*\{\s*)(?:[a-z0-9 \._\*\n\r\t\s:;,\-#%\(\)\/!='\"]+)(?=\s*\}\s*)/im",
		$file,$matches,PREG_SET_ORDER   );
		
		 foreach($matches as $match)
		 {
			$element = trim($match[1]);
			$index =array_search($element,$allowed_elements);
			$type = '';
			
			if($element == '.' )
			{
				$type = 'class'; 
			}
			else if($element =='#')
			{
				$type = 'id';
			}		
			else
			{
				$type =  ($match[2] == '.') ? 'class' : 'id';
			}
			
			if($index)
			{
				$element = ($element == '.' || $element == '#') ? 'P' : 	$allowed_elements[$index];
			
				//$match[3] = preg_replace('/(?![a-z0-9,]+\s+)(' . $elem_list .')(?!_|\-)/i', '', $match[3]);
	
				$names = 	explode(",",$match[3]);	
				$current_names =  array();	
				$names[0] = $match[1].$match[2].$names[0];
				
						
				foreach($names  as $name)
				{
				
					
					$name = trim($name);
					
					if(preg_match('/^('.$elem_list.')(\.|#)?[a-z0-9]/i', $name))
						$name = preg_replace('/^('.$elem_list.')(\.|#)?/i','', $name);
					else
					 continue;
				 
					if (!preg_match('/^[A-Z0-9_\-]+(\s+|\.|#)[A-Z0-9_\-\.#]+/i',$name))
					{
					
						$key = array_search($name,$this->_nam);
						if(!in_array($name,$current_names))
						 {
						 
						
							 if(!$key && $name != "" )
							 {
								 array_push($this->_nam,$name);
								 array_push($this->_elem,$element);
								 array_push($this->_prop,$type);
							 }
							 array_push($current_names,$name);
					
						}
					}
				
				}	
				
				
			}
		}	
	
	} //end function
	
	function getPath(& $errors = '',$type = "content")
	{
	
		//Get parameter options for template CSS
	    
		 $params  = $this->_params;

		$content_css		=	$params->get($type. '_css', 1 );
		$content_css_custom	=	$params->def( $type.'_css_custom', '' );

	
		
		$db = JFactory::getDBO();
		
		$query	= $db->getQuery(true);

		$query->select('template');
		$query->from('#__template_styles');
		$query->where('client_id=0 AND home=1');

		$db->setQuery( $query );
		$template = $db->loadResult();

		//For some reason the Beez template are using General.css instead
		//of template.css, and the template.css file is setup to fail?!
		//This code checks to see what template we are using and switch
		//to General.css to solve the issue.
		//
		//By Mark Smeed - For 1.6 ONLY
		if( in_array( $template, array( 'beez_20', 'beez5', 'beez3' ) ) && ( $content_css == 1 && $editor_css == 0 )  )
		{
			$content_css = 0;
			$content_css_custom = 'templates/'.$template.'/css/general.css';
			$this->_default_beez_template = $template;
		}//end if
		
		if ( $content_css ) 
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
					$errors .=	'<span style="color: red;">Warning: the JCK Editor cannot find a default template.css style-sheet to use. Please see: <a  href="http://www.joomlackeditor.com/downloads/jck-editor/installation-guide?start=8">Installation guide</a></span><br/>';
					//$errors .= '<span style="color: red;">Warning: ' . JPATH_SITE . '/templates/'.$template.'/css/template.css' . ' or ' . JPATH_SITE . '/templates/'.$template.'/css/template.css.php does not appear to be a valid file. Reverting to JoomlaCK\'s default styles</span><br/>'; AW 15/07/11
				}//end if valid file
	
			/* Is the content_css == 0 or 1 then use FCK's default */
			if( $errors !== "" )
			{
				$content_css = 'plugins/editors/jckeditor/contents.css';
			}//end if 
	
	
		}
		else 
		{
			if ( $content_css_custom )
			{
               
			              
				$hasRoot = strpos(' ' . strtolower($content_css_custom),strtolower(JPATH_SITE));
				$file_path = ($hasRoot ? '' : JPATH_SITE) .  ($hasRoot || substr($content_css_custom,0,1) == DS  ? '' : DS) .
				$content_css_custom;
           
		 	   
				if( is_file(  $file_path) )
				{
					$content_css =  $file_path;
					$content_css = str_replace(strtolower(JPATH_SITE) . DS,'',strtolower($content_css_custom));
				} else
				{
					//$errors .= '<span style="color: red;">Warning: ' .  $file_path . ' does not appear to be a valid file.</span><br/>'; AW 15/07/11
					$errors .=	'<span style="color: red;">Warning: the JCK Editor cannot find a default template.css style-sheet to use. Please see: <a  href="http://www.joomlackeditor.com/downloads/jck-editor/installation-guide?start=8">Installation guide</a></span><br/>';
					$content_css = 'plugins/editors/jckeditor/contents.css';
				}//end if valid file
					
			} 
			else 
			{
				$content_css = 'plugins/editors/jckeditor/contents.css';
			}//end if $content_css_custom
			/*write to xml file and read from css asnd store this file under editors*/
		}//end if $content_css || $editor_css

		return $content_css;
	}	
	
}
?>