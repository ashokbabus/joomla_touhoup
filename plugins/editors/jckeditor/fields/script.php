<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');

class JFormFieldScript extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'script';

	/**
	 * Add some script to the document
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		$root 	= ( isset( $this->element['root'] ) && (string)$this->element['root'] == 'true' ) 	? JURI::root() 			: '';
		$script = ( isset( $this->element['script'] ) ) 											? (string)$this->element['script'] 		: '';
		$file 	= ( isset( $this->element['script_file'] ) ) 										? (string)$this->element['script_file'] : '';
		$style 	= ( isset( $this->element['style'] ) ) 												? (string)$this->element['style'] 		: '';
		$css 	= ( isset( $this->element['css_file'] ) ) 											? (string)$this->element['css_file'] 	: '';
		$doc	= JFactory::getDocument();

		if( $script )
		{
			$doc->addScriptDeclaration( $script );
		}//end if
		
		if( $file )
		{
			$doc->addScript( $root . $file );
		}//end if
		
		if( $style )
		{
			$doc->addStyleDeclaration( $style );
		}//end if
		
		if( $css )
		{
			$doc->addStyle( $root . $css );
		}//end if
	}//end function
	
	protected function getLabel()
	{
		return '';
	}//end function
}//end class