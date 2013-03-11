<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_PLATFORM') or die;

class JCKConfigHandlerDualAddColorList 
{
	function getOptions($key,$value,$default,$node,$params,$pluginName)
	{
		$options = '';
		
		$is_a_object = $node->attributes('is_object');
		$is_a_array = $node->attributes('is_array');
		$separator = $node->attributes('separator');
		
		if(!$separator)
			$separator = ','; //default to a comma separated list
			
		if(empty($value))
		{
			$value = array();
			foreach($node->children()as $option)
			{
				if($option->name() != 'option')
					continue;
					
				$tmp = $option->data();
				$tmp .= '/'. $option->attributes('value');
				$value[] = $tmp;
			}
		}
		else
		{
			$texts = $params->get($key.'_text');
			
			foreach($value as $k=>$v)
			{
				$tmp = $texts[$k];
				$tmp .= '/'.$v;
				$value[$k] = $tmp;
			}
		
		}
	
		$value = implode($separator,$value);
		
		if($is_a_object)
		  $value = '{'.$value.'}';

		if($is_a_array)
		  $value = '['.$value.']';	
		  
		$options .= "\"$key='".$value."'\",";  
		
		return $options;
	}
}