<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

jimport( 'joomla.html.parameter' );

class JCKHelper
{
	protected static $errors = array();

	/**
	 * -PC- 
	 * Centralise error handling so when Joomla change their API again we only change in one place!
	 * Throw a Joomla error - JCKHelper::error( $results[0]->msg );
	 *
	 * $message		= (string) The message to throw
	 * $type		= (string) The type of error
	 * $location	= (string) How to throw the error. Currently - 'database', 'echo', 'formattedtext', 'messagequeue', 'syslog', 'w3c'
	 */
	public static function error( $message = false, $type = 'error', $location = 'messagequeue' )
	{
		if( !$message || in_array( $message, self::$errors ) ) return false;

		// Prevent throwing the same error multiple times
		self::$errors[] = $message;

		switch( $type )
		{
			default :
			case 'warning' :	// Can't throw warning thanks to Joomla
			case 'error' :
				$level = JLog::ERROR;
				break;
			case 'notice' :
				$level = JLog::NOTICE;
				break;
			case 'message' :
			case 'info' :
				$level = JLog::INFO;
				break;
		}//end switch

		// Generate unique ID to avoid Joomla bug of throwing Joomla depreciated messages as well
		$id = time() . chr( 95 ) . base_convert( mt_rand( 0x19A100, 0x39AA3FF ), 10, 36 );

		//JLog::addLogger( array( 'logger' => $location, 'com_jckman' => $id ), $level );
		JLog::add( $message, $level );

		return true;
	}//end function

	public static function addSubmenu( $vName = false )
	{
		$canDo 		= JCKHelper::getActions();
		$subMenus 	= array(
							'Control Panel' 	=> array( 'extension' => 'cpanel', 						'permission' => '', 				'hideinmob' => false, 	'hideinipad' => false ),
							'Plugin Manager' 	=> array( 'extension' => 'list', 						'permission' => '', 				'hideinmob' => false, 	'hideinipad' => false ),
							'Installer' 		=> array( 'extension' => 'install', 					'permission' => 'jckman.install', 	'hideinmob' => false, 	'hideinipad' => false ),
							'Uninstaller' 		=> array( 'extension' => 'plugin', 						'permission' => 'jckman.uninstall', 'hideinmob' => false, 	'hideinipad' => false ),
							'System Check' 		=> array( 'extension' => 'cpanel&taskbtn=system', 		'permission' => 'core.edit', 		'hideinmob' => true, 	'hideinipad' => true ),
							'Layout Manager'	=> array( 'extension' => 'toolbars', 					'permission' => '', 				'hideinmob' => true, 	'hideinipad' => true ),
							'Restore Backup' 	=> array( 'extension' => 'import', 						'permission' => 'core.edit', 		'hideinmob' => true, 	'hideinipad' => true ),
							'Create Backup' 	=> array( 'extension' => 'cpanel&taskbtn=export', 		'permission' => '', 				'hideinmob' => true, 	'hideinipad' => true ),
							'Sync' 				=> array( 'extension' => 'cpanel&taskbtn=sync', 		'permission' => 'jckman.sync', 		'hideinmob' => false, 	'hideinipad' => false ),
							'JCK Editor' 		=> array( 'extension' => 'cpanel&taskbtn=editor', 		'permission' => '', 				'hideinmob' => false, 	'hideinipad' => false )
						);

		foreach( $subMenus as $name => $params ) 
		{
			// hide in iPad
			if( !$params['hideinipad'] || ( $params['hideinipad'] && !self::isiPad() ) )
			{
				// hide in mobile
				if( !$params['hideinmob'] || ( $params['hideinmob'] && !self::isMobile() ) )
				{
					// perform any permissions
					if( !$params['permission'] || $canDo->get($params['permission']) )
					{
						JHtmlSidebar::addEntry(JText::_( $name ), 'index.php?option=com_jckman&view='.$params['extension'], ($params['extension'] == $vName));
					}
				}
			}
		}
	}//end function

	public static function isiPad()
	{
		$browser = JBrowser::getInstance();

		return ( stripos( $browser->getAgentString(), 'iPad' ) === false ) ? false : true;
	}//end function

	public static function isMobile()
	{
		$browser 	= JBrowser::getInstance();
		$isMob		= false;
		$isMob		= ( $browser->isMobile() ) ? true : $isMob;
		$isMob		= ( stripos( $browser->getAgentString(), 'iPhone' ) === false ) ? $isMob : true;
		//$isMob		= ( stripos( $browser->getAgentString(), 'YOUR_PHONE_HERE' ) === false ) ? $isMob : true;

		return $isMob;
	}//end function
	
	public static function fixBug()
	{
		// FIX JOOMLA BUG! - NONE OF THEIR DISABLED LEFT HAND MENU's HAVE CLOSING TAGS SO STOP OUR PAGE DISTORTING HERE
		// TODO: TELL JOOMLA & REMOVE BELOW LINE
		echo '</a>';
	}//end function

	static function & getTable( $name, $prefix = 'JCKTable', $config = array())
	{
				
		$path = JPATH_COMPONENT.DS.'tables';
		JTable::addIncludePath($path);

		// Clean the name
		$prefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );

		//Make sure we are returning a DBO object
		if (!array_key_exists('dbo', $config))  {
			$config['dbo'] = JFactory::getDBO();
		}

		$instance =@ JTable::getInstance($name, $prefix, $config );
		return $instance;
	}

	 static function & geTtoolbarParams($editor,$args = array())
	 {
		 
		 if( count($args) > 1) 
		{
			$row = $args[1];		
		}
		
		        
	   	if(is_a($args[0] ,'JParameter'))
		{
			$params = $args[0];
		}
		else
		{   	if( $row) 
			{
			    $params = new JParameter($row->params);		
			}
			else
			{
				$row = & JCKHelper::getTable('toolbar');
				// load the row from the db table
				$row->load( $args[0]);
				//get toolbar parameter
				$params = new JParameter($row->params);
			}
		}
		
		$editor_params   = new JParameter($editor->params);
		$toolbar = $params->get('toolbar',$row->name);
		$skins = $params->get('skin', $editor_params->def( 'skin','office2003'));
		$width = $params->get('wwidth', $editor_params->def( 'wwidth','100%'));
		
	
		$editor_params->set( 'toolbar',$toolbar);
		$editor_params->set( 'skin', $skins );
		$editor_params->set( 'wwidth', $width);
	    $editor_params->Set( 'hheight',300);			
		return $editor_params;
	 }
	
	
	
	static function & getEditorPluginConfig($namspace = 'config')
	{
	    static $config;
        
        if(!isset($config))
        {
            $path =  CKEDITOR_LIBRARY;
		
		    require_once($path.DS.'plugins.php');
		    require_once($path.DS.'plugins'.DS.'toolbarplugins.php');
		
	    	$config = new JRegistry();
		
		    $pluginConfig = new JCKToolbarPlugins();
			
		    $config->loadObject($pluginConfig);
		    $data = $config->toObject();
		    $properties = get_object_vars($data);
		
            foreach($properties as $key=>$value)
            {						
                if(strpos('p'.$key,'_'))
                unset($data->$key);	
            }
			
			//Forcibly remove the save plugin due to it causing the icon
			//to disappear in editor version 6.0.4+
			unset( $data->save );	
		
	  	    return 	$config;
        }  
        
        return $config;
    }

	static function & getEditorToolbars()
	{
		$path =  CKEDITOR_LIBRARY.DS.'toolbar';
		
		$files = JFolder::files($path);

		$toolbars = array();

		foreach($files as $file)
		{
			if(strpos($file,"index") === false && strpos(strrev($file), 'php.') === 0) 
			{
				$toolbars[] = preg_replace('/\.php$/','',$file); 
			}	
		}
		return $toolbars;
	}	

	static function getNextAvailablePluginRowId()
	{
		$db = JFactory::getDBO();
		
		$db->setQuery('SELECT `row` AS id,count(`row`) AS total FROM `#__jckplugins`'.
					' GROUP BY row'.
					' HAVING `row` > 2 ORDER BY `row` DESC LIMIT 1');
		$row = $db->loadObject();
		
		if(!$row && is_null($row))
		{
		 $row = new stdclass;
		 $row->id = 4;
		 $row->order = 1;
		}
		
		$id = $row->id;
		if($row->total = 26)
		  $id++;
		 
		return $id;
	}

	static function getNextLayoutRow($toolbarid)
	{
		$db = JFactory::getDBO();
		
		$db->setQuery('SELECT `row` AS rowid,MAX(`ordering`) +1  AS rowordering FROM `#__jcktoolbarplugins`'
					.' WHERE `toolbarid`='.(int) $toolbarid
					.' GROUP BY `row`'
					.' ORDER BY `row` DESC LIMIT 1');
		$row = $db->loadObject();
		
		if(!$row && is_null($row))
		{
		 $row = new stdclass;
		 $row->rowid = 4;
		 $row->rowordering = 1;
		}
		
		return $row;
	}

	/**
	 * List of plugins to be hidden in list & edit views
	 */
	public static function getHiddenPlugins( $asString = false )
	{
		$hide = array( 'about', 'save', 'flash' );

		return ( $asString ) ? '"' . implode( '","', $hide ) . '"' : $hide;
	}//end function

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = JAccess::getActions('com_jckman');

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, 'com_jckman'));
		}

		return $result;
	}//end function

	/**
	 * Get a list of filter options for the state of a module.
	 *
	 * @return	array	An array of JHtmlOption elements.
	 */
	public static function getStateOptions()
	{
		// Build the filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option',	'1',	JText::_('JPUBLISHED'));
		$options[]	= JHtml::_('select.option',	'0',	JText::_('JUNPUBLISHED'));
		//$options[]	= JHtml::_('select.option',	'-2',	JText::_('JTRASHED'));
		return $options;
	}

	/**
	 * Get an XML document
	 *
	 * @param   string  $type     The type of XML parser needed 'DOM', 'RSS' or 'Simple'
	 * @param   array   $options  ['rssUrl'] the rss url to parse when using "RSS", ['cache_time'] with '
	 *                             RSS' - feed cache time. If not defined defaults to 3600 sec
	 *
	 * @return  object  Parsed XML document object
	 *
	 * @deprecated    12.1   Use JXMLElement instead.
	 * @see           JXMLElement
	 */
	public static function getXMLParser($type = '', $options = array())
	{
		$doc = null;

		switch (strtolower($type))
		{
			case 'simple':
				require_once( dirname( __FILE__ ) . DS . 'helpers' . DS . 'simplexml.php' );
				$doc = new JSimpleXML;
				break;

			case 'dom':
				JCKHelper::error( JText::_('JLIB_UTIL_ERROR_DOMIT') );
				$doc = null;
				break;

			default:
				$doc = null;
		}

		return $doc;
	}

	/**
	 * Reads a XML file.
	 *
	 * @param   string   $data    Full path and file name.
	 * @param   boolean  $isFile  true to load a file or false to load a string.
	 *
	 * @return  mixed    JXMLElement on success or false on error.
	 *
	 * @see     JXMLElement
	 * @since   11.1
	 * @todo    This may go in a separate class - error reporting may be improved.
	 */
	public static function getXML($data, $isFile = true)
	{
		require_once( dirname( __FILE__ ) . DS . 'helpers' . DS . 'xmlelement.php' );

		// Disable libxml errors and allow to fetch error information as needed
		libxml_use_internal_errors(true);

		if ($isFile)
		{
			// Try to load the XML file
			$xml = simplexml_load_file($data, 'JXMLElement');
		}
		else
		{
			// Try to load the XML string
			$xml = simplexml_load_string($data, 'JXMLElement');
		}

		if (empty($xml))
		{
			// There was an error
			JCKHelper::error( JText::_('JLIB_UTIL_ERROR_XML_LOAD') );

			if ($isFile)
			{
				JCKHelper::error( $data );
			}

			foreach (libxml_get_errors() as $error)
			{
				JCKHelper::error( 'XML: ' . $error->message );
			}
		}

		return $xml;
	}

	/**
	 * Parse a XML install manifest file.
	 *
	 * XML Root tag should be 'install' except for languages which use meta file.
	 *
	 * @param   string  $path  Full path to XML file.
	 *
	 * @return  array  XML metadata.
	 *
	 * @since   12.1
	 */
	public static function parseXMLInstallFile($path)
	{
		// Read the file to see if it's a valid component XML file
		$xml = simplexml_load_file($path);
		if (!$xml)
		{
			return false;
		}

		// Check for a valid XML root tag.

		// Extensions use 'extension' as the root tag.  Languages use 'metafile' instead

		if ($xml->getName() != 'extension' && $xml->getName() != 'install' && $xml->getName() != 'metafile')
		{
			unset($xml);
			return false;
		}

		$data = array();

		$data['name'] = (string) $xml->name;

		// Check if we're a language. If so use metafile.
		$data['type'] = $xml->getName() == 'metafile' ? 'language' : (string) $xml->attributes()->type;

		$data['creationDate'] = ((string) $xml->creationDate) ? (string) $xml->creationDate : JText::_('Unknown');
		$data['author'] = ((string) $xml->author) ? (string) $xml->author : JText::_('Unknown');

		$data['copyright'] = (string) $xml->copyright;
		$data['authorEmail'] = (string) $xml->authorEmail;
		$data['authorUrl'] = (string) $xml->authorUrl;
		$data['version'] = (string) $xml->version;
		$data['description'] = (string) $xml->description;
		$data['group'] = (string) $xml->group;

		return $data;
	}
}//end class

jimport('joomla.application.component.helper');

abstract class JCKModuleHelper extends JModuleHelper
{
	public static function &getModules($position)
	{
		$app		= JFactory::getApplication();
		$position	= strtolower($position);
		$result		= array();

		$modules = self::_load();

		$total = count($modules);
		for ($i = 0; $i < $total; $i++)
		{
			if ($modules[$i]->position == $position) {
				$result[] = &$modules[$i];
			}
		}
		if (count($result) == 0)
		{
			if (JRequest::getBool('tp') && JComponentHelper::getParams('com_templates')->get('template_positions_display'))
			{
				$result[0] = self::getModule('mod_'.$position);
				$result[0]->title = $position;
				$result[0]->content = $position;
				$result[0]->position = $position;
			}
		}
		
		return $result;
	}
	
	/* Load published modules
	 *
	 * @return	array
	 */
	protected static function &_load()
	{
		static $clean;

		if (isset($clean)) {
			return $clean;
		}

		$Itemid 	= JRequest::getInt('Itemid');
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$groups		= implode(',', $user->getAuthorisedViewLevels());
		$lang 		= JFactory::getLanguage()->getTag();
		$clientId 	= (int) $app->getClientId();

		$cache 		= JFactory::getCache ('com_modules', '');
		$cacheid 	= md5(serialize(array('com_jckman', $groups, $clientId, $lang)));


		if (!($clean = $cache->get($cacheid))) {
			$db	= JFactory::getDbo();

			$query =  $db->getQuery(true); //new JDatabaseQuery;
			$query->select('id, title, module, position, content, showtitle, params, mm.menuid');
			$query->from('#__modules AS m');
			$query->join('LEFT','#__modules_menu AS mm ON mm.moduleid = m.id');
			$query->where('m.published = 1');

			$date = JFactory::getDate();
			$now = $date->toSQL();
			$nullDate = $db->getNullDate();
			$query->where('(m.publish_up = '.$db->Quote($nullDate).' OR m.publish_up <= '.$db->Quote($now).')');
			$query->where('(m.publish_down = '.$db->Quote($nullDate).' OR m.publish_down >= '.$db->Quote($now).')');

			$query->where('m.access IN ('.$groups.')');
			$query->where('m.client_id = '. $clientId);
			$query->where('(mm.menuid = '. (int) $Itemid .' OR (mm.menuid <= 0 OR mm.menuid IS NULL))'); //fix as this is suppose to be a LEFT JOIN!!! 

			// Filter by language
			if ($app->isSite() && $app->getLanguageFilter()) {
				$query->where('m.language IN (' . $db->Quote($lang) . ',' . $db->Quote('*') . ')');
			}

			$query->order('position, ordering');

			// Set the query
			$db->setQuery($query);
			if (!($modules = $db->loadObjectList())) {
				JCKHelper::error( JText::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $db->getErrorMsg()) );
				return false;
			}

			// Apply negative selections and eliminate duplicates
			$negId	= $Itemid ? -(int)$Itemid : false;
			$dupes	= array();
			$clean	= array();
			for ($i = 0, $n = count($modules); $i < $n; $i++)
			{
				$module = &$modules[$i];

				// The module is excluded if there is an explicit prohibition, or if
				// the Itemid is missing or zero and the module is in exclude mode.
				$negHit	= ($negId === (int) $module->menuid)
						|| (!$negId && (int)$module->menuid < 0);

				if (isset($dupes[$module->id]))
				{
					// If this item has been excluded, keep the duplicate flag set,
					// but remove any item from the cleaned array.
					if ($negHit) {
						unset($clean[$module->id]);
					}
					continue;
				}
				$dupes[$module->id] = true;

				// Only accept modules without explicit exclusions.
				if (!$negHit)
				{
					//determine if this is a custom module
					$file				= $module->module;
					$custom				= substr($file, 0, 4) == 'mod_' ?  0 : 1;
					$module->user		= $custom;
					// Custom module name is given by the title field, otherwise strip off "com_"
					$module->name		= $custom ? $module->title : substr($file, 4);
					$module->style		= null;
					$module->position	= strtolower($module->position);
					$clean[$module->id]	= $module;
				}
			}
			unset($dupes);
			// Return to simple indexing that matches the query order.
			$clean = array_values($clean);

			$cache->store($clean, $cacheid);
		}

		return $clean;
	}
}

jimport( 'joomla.form.form' );
class JCKForm extends JForm
{
	/**
	 * Method to get an instance of a form.
	 *
	 * @param	string	$name		The name of the form.
	 * @param	string	$data		The name of an XML file or string to load as the form definition.
	 * @param	array	$options	An array of form options.
	 * @param	string	$replace	Flag to toggle whether form fields should be replaced if a field
	 *								already exists with the same group/name.
	 * @param	string	$xpath		An optional xpath to search for the fields.
	 *
	 * @return	object	JForm instance.
	 * @throws	Exception if an error occurs.
	 * @since	1.6
	 */
	public static function getInstance($name, $data = null, $options = array(), $replace = true, $xpath = false)
	{
		// Reference to array with form instances
		$forms = &self::$forms;

		// Only instantiate the form if it does not already exist.
		if (!isset($forms[$name]))
		{
			$data = trim($data);

			if (empty($data))
			{
				throw new InvalidArgumentException(sprintf('JForm::getInstance(name, *%s*)', gettype($data)));
			}

			// Instantiate the form.
			$forms[$name] = new JCKForm($name, $options);

			// Load the data.
			if (substr(trim($data), 0, 1) == '<')
			{
				if ($forms[$name]->load($data, $replace, $xpath) == false)
				{
					throw new RuntimeException('JForm::getInstance could not load form');
				}
			}
			else
			{
				if ($forms[$name]->loadFile($data, $replace, $xpath) == false)
				{
					throw new RuntimeException('JForm::getInstance could not load file');
				}
			}
		}

		return $forms[$name];
	}

	/**
	 * Method to get a form field represented as an XML element object.
	 *
	 * @param	string	$name	The name of the form field.
	 * @param	string	$group	The optional dot-separated form group path on which to find the field.
	 *
	 * @return	mixed	The XML element object for the field or boolean false on error.
	 * @since	1.6
	 */
	protected function findField($name, $group = null)
	{
		$element = false;
		$fields = array();

		// Make sure there is a valid JForm XML document.
		if (!($this->xml instanceof SimpleXMLElement))
		{
			return false;
		}

		// Let's get the appropriate field element based on the method arguments.
		if ($group)
		{

			// Get the fields elements for a given group.
			$elements = &$this->findGroup($group);

			// Get all of the field elements with the correct name for the fields elements.
			foreach ($elements as $element)
			{
				// If there are matching field elements add them to the fields array.
				if ($tmp = $element->xpath('descendant::field[@name="' . $name . '"]'))
				{
					$fields = array_merge($fields, $tmp);
				}
			}

			// Make sure something was found.
			if (!$fields)
			{
				return false;
			}

			// Use the first correct match in the given group.
			$groupNames = explode('.', $group);
			foreach ($fields as &$field)
			{
				// Get the group names as strings for ancestor fields elements.
				$attrs = $field->xpath('ancestor::fields[@name]/@name');
				$names = array_map('strval', $attrs ? $attrs : array());

				// If the field is in the exact group use it and break out of the loop.
				if ($names == (array) $groupNames)
				{
					$element = &$field;
					break;
				}
			}
		}
		else
		{
			// Get an array of fields with the correct name.
			$fields = $this->xml->xpath('//field[@name="' . $name . '"]');

			// Make sure something was found.
			if (!$fields)
			{
				return false;
			}

			// Search through the fields for the right one.
			foreach ($fields as &$field)
			{
				// -PC- exact copy of JForm findField except their ancestor lookup was blocking the bind for our plugins
				// If we find an ancestor fields element with a group name then it isn't what we want.
				if ($field->xpath('ancestor::fields[@name="params"]'))
				{
					$element = &$field;
					break;
				}
			}
		}

		return $element;
	}

	/**
	 * Method to get the value of a field.
	 *
	 * @param	string	$name		The name of the field for which to get the value.
	 * @param	string	$group		The optional dot-separated form group path on which to get the value.
	 * @param	mixed	$default	The optional default value of the field value is empty.
	 *
	 * @return	mixed	The value of the field or the default value if empty.
	 * @since	1.6
	 */
	public function getValue($name, $group = null, $default = null)
	{
		$return = $this->data->get($name, $default);
		return $return;
	}
}//end class JCKForm