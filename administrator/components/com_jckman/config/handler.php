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

class JCKConfigHandler 
{
	static function getInstance($name = NULL) 
	{
		static $instances = array();

		if(is_null($name))
		{
			if(!isset($instances[$name]))
            	$instances['_self'] = new JCKConfigHandler();
            return $instances['_self'];	
		}

		if(!isset($instances[$name]))
		{
			$path = JPATH_ADMINISTRATOR.'/components/com_jckman/config/handlers/'.$name.'.php';
			if(!file_exists($path))
            {
				if(!isset($instances[$name]))
                    $instances['_self'] = new JCKConfigHandler();
                return $instances['_self'];	
            }
            require $path;
			$classname = 'JCKConfigHandler'.$name; 
			$instances[$name] = new $classname; 
		}
		return $instances[$name];
	}

	function getOptions($key,$value,$default,$node,$params,$pluginName)
	{
		$options = '';

		if(is_array($value))
		{
			$is_a_object = $node->attributes('is_object');
			$is_a_array = $node->attributes('is_array');
			$separator = $node->attributes('separator');
			
			if(!$separator)
				$separator = ','; //default to a comma separated list

			$value = implode($separator,$value);

			if($is_a_object)
				$value = '{'.$value.'}';

			if($is_a_array)
				$value = '['.$value.']';

			$options .= "\"$key='".$value."'\",";  			
		}
		elseif(is_numeric($value))
			$options .= "\"$key=$value\",";
		else
			$options .= "\"$key='".$value."'\",";

		return $options;
	}
}