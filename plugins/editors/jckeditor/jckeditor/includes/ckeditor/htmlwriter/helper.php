<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2010 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

jckimport('ckeditor.htmlwriter.htmlwriter');
jckimport('ckeditor.htmlwriter.javascript');

class JCKHtmlwriterHelper
{

	static function EditorTextArea($id,$name,$content,$buttons,$context,$arributes = array(),$asset = null, $author = null)
    {
 
		$html =  JCKHtmlwriter::textarea($id,$name,$content,$arributes);
		
		//load CKEditor script
		$javascript = new JCKJavascript();
		
		$id = JCKOutput::fixId($id);
		
		$javascript->addScriptDeclaration(
		
		'window.addDomReadyEvent.add(function()
				{	
					CKEDITOR.config.expandedToolbar = true;
					CKEDITOR.tools.callHashFunction("'.$id.'","'.$id.'");
				});');
		
		 $html .= $javascript->toString();
				 
		
		//set event handlers
		$args['name'] = $id;
		$args['event'] = 'onGetInsertMethod';

		$results[] = $context->update($args);
		
		foreach ($results as $result) {
			if (is_string($result) && trim($result)) {
				$html .= $result;
			}
		}
			
		 //Get buttons
		if(!empty($buttons) || is_array($buttons) && !array_key_exists( 0, $buttons ) )
		{
			
			// Load modal popup behavior
			JHTML::_('behavior.modal', 'a.modal-button');
			
			$editor = JFactory::getEditor('jckeditor');
								
		 	$plugins = $editor->getButtons($id,$buttons,$asset, $author);
			$buttons = '';
			$container = '';
			if(version_compare(JVERSION, '3.0', 'ge'))
			{
				foreach($plugins as $plugin)
				{
					$className	=	($plugin->get('modal')) ? "modal-button btn" : 'btn';
					$url		= 	($plugin->get('link')) ? JURI::base().$plugin->get('link') : '';
					$click		= 	($plugin->get('onclick')) ? $plugin->get('onclick') : 'IeCursorFix(); return false;';
					$options 	=  	$plugin->get('options');
					$content	=	$plugin->get('text'); 
					$buttonName = 	$plugin->get('name'); 
					$content = '<i class="icon-' . $buttonName. '"></i>'.$content;
					$linkAttributes = array("rel"=>$options,'onclick'=>$click);
					$buttons 	.= JCKHtmlwriter::link($url,$content,'',$className,$linkAttributes);
				}
				$innerContainer = JCKHtmlwriter::DivContainer($buttons,'','btn-toolbar');
				$container	= JCKHtmlwriter::DivContainer($innerContainer,'editor-xtd-buttons','btn-toolbar pull-left');
			}
			else
			{
				foreach($plugins as $plugin)
				{
					$className	=	($plugin->get('modal')) ? "modal-button" : '';
					$url		= 	($plugin->get('link')) ? JURI::base().$plugin->get('link') : '';
					$click		= 	($plugin->get('onclick')) ? $plugin->get('onclick') : '';
					$options 	=  	$plugin->get('options');
					$content	=	$plugin->get('text'); 
					$buttonName = 	$plugin->get('name'); 
					$buttons 	.=  JCKHtmlwriter::buttonModalLink($url,$content,$options,$buttonName,$className,$click,array("class"=>"button2-left"));
				}
				$container	= JCKHtmlwriter::DivContainer($buttons,'editor-xtd-buttons');	
			}
			$html .= $container; 
		}
		
		return $html;

	}

}

?>