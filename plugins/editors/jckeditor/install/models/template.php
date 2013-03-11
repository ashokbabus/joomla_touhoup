<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * Install Model
 *
 * @package    JCK Editor
 * @subpackage JCK.install Wizard
 */
JCKLoader::loadExtendClass('model'); 
 
class InstallModelTemplate extends JCKModel
{
	
	private $_editor; 
	
	private $_basepath;
	
	private $_templates;
	
	private $_defaultTemplate;
	
	private $_stylesheets;
	
	private $_comboStylesheets;
	
	private $_excludeFilter;
	
	private $_overwrite = false;
	private $_useAutomaticOnly = false;
	
	public function __construct($config = array())
	{
				
		if( defined('JLEGACY_CMS') )
		{
			$sql =  "SELECT id,params FROM #__plugins WHERE element = 'jckeditor' AND folder ='editors'" ;
		
		} else
		{
			$sql =  "SELECT extension_id as id, params FROM #__extensions WHERE element = 'jckeditor' AND folder ='editors'" ;
		
		}//end if
		
		$database =  JFactory::getDBO();   
		$database->setQuery( $sql );
		$result = $database->loadObject();
		$this->_editor = $result;
		
		$this->_basepath = JPATH_CONFIGURATION.DS.'templates';
		
		$basepath = $this->_basepath;
		
		if( defined('JLEGACY_CMS') )
		{
			$sql =  "SELECT template as value,template as text, case when client_id = 0 AND menuid = 0 then 1 else 0 end as system_default FROM #__templates_menu WHERE client_id = 0" ;
		} else
		{
			$sql =  "SELECT template as value,template as text, case when client_id = 0 AND home = 1 then 1 else 0 end as system_default FROM #__template_styles WHERE client_id = 0" ;
		}//end if
	
		$database->setQuery( $sql );
		$result = $database->loadObjectList();

		$this->_templates = $result;
		
		for($i = 0; $i < count($this->_templates);$i++)
		{
			if($this->_templates[$i]->system_default)
			{
			 $this->_defaultTemplate = $this->_templates[$i]->value;
			 break;
			}	
		}
				
		$this->_excludeFilter = 'ieonly|ie7only|ie8only|_ie|_rtl|ie7|ie8|_Konqueror|_mozilla|_opera|print';
		
		parent::__construct($config);
	}
	
	
	private function _getParams()
	{
			
		static $registry = NULL;
			
		if(is_null($registry))
		{
			$registry = new JRegistry($this->_editor->params);
		}		
		return $registry;
	}
	
	
	private function _getTemplateSytlesheetFiles($path)
	{
		return JFolder::files($path, $filter = '\.css$', false, false,array('.svn', 'CVS','.DS_Store','__MACOSX','system'));
		
	}
	
	public function getTemplateList()
	{
		
		$params = $this->_getParams();
		$default = 'Automatic';
		
		
		
		if(!$params->get('content_css',0))
		{
			$content_css_custom = $params->get('content_css_custom','');
				
			if($content_css_custom)
			{
				$default =  $this->_defaultTemplate;
				$content_css_custom  = trim($content_css_custom);
				preg_match('/templates\/([^\/]*?)\/css\/[^\/]*.css$/',$content_css_custom,$match);
				if(isset($match[1]) && $match[1] != $this->_defaultTemplate)
					$default =  $match[1];
			}
				
			
		}
	
		$options = array(JHTML::_('select.option', 'Automatic', 'Automatic'));
	
		
		foreach($this->_templates as $template)
		{
			$options[] =  JHTML::_('select.option', $template->value, $template->text);
		} 
		
		
		$javascript = "onchange=\"Joomla.changeDynaList( 'content_css_custom', templateStylesheets, document.adminForm.template.options[document.adminForm.template.selectedIndex].value, 0, 0);Joomla.changeDynaList( 'styles_css_custom', richcomboStylesheets, document.adminForm.template.options[document.adminForm.template.selectedIndex].value, 0, 0);\"";	
			
		$list = JHTML::_('select.genericlist',  $options, 'template', 'class="box" size="1" '.$javascript,'value','text',$default);
	

	
		$templatestylesheets = array ();
		$templatestylesheets['Automatic'] = array ();
		$templatestylesheets['Automatic'][] = JHTML::_('select.option', 'Automatic', 'Automatic');
			
		foreach($this->_templates as $template)
		{
			$name = $template->value;
			$path = $this->_basepath.DS.$name.DS.'css';
			$stylesheets = $this->_getTemplateSytlesheetFiles($path);
			for($i= 0; $i < count($stylesheets); $i++)
			{
				$stylesheet = $stylesheets[$i];
				
				if(!preg_match('/.*('.$this->_excludeFilter.')\./i',$stylesheet))
				{
					$stylesheetPath =  'templates'.DS.$name.DS.'css'.DS.$stylesheet;
					$stylesheet = str_replace('.css','',$stylesheet);
					$templatestylesheets[$name][] =  JHTML::_('select.option', $stylesheetPath, $stylesheet);
				}	
			}	
		
		} 
				
		$this->_stylesheets = $templatestylesheets;
		
		foreach($templatestylesheets as $name => $items)
		{
			
			$items[] =  JHTML::_('select.option', 'None', 'None');
			$templatestylesheets[$name] = $items;
		}
		
		$this->_comboStylesheets = $templatestylesheets;
	
	
		return $list;
	}
	
	
	public function getStylesheetList()
	{
		
		$params = $this->_getParams();
	
		$template =  $this->_defaultTemplate;
		$default = 'Automatic';
		
		if(!$params->get('content_css',0))
		{
			$content_css_custom = $params->get('content_css_custom','');
			if($content_css_custom)
			{
				$content_css_custom  = trim($content_css_custom);
				preg_match('/templates\/([^\/]*?)\/css\/([^\/]*).css$/',$content_css_custom,$match);
							
				if(isset($match[1]) && $match[1] != $this->_defaultTemplate)
				{
					$template =  $match[1];
					$this->_defaultTemplate = $template;
				}
				if(isset($match[2]))
					$default =  $match[2];
			}	
		}
		
		$defaultstylesheets  = array();
		
		
		
		
		if($default != 'Automatic')
		{
			$path = $this->_basepath.DS.$template.DS.'css';
			$defaultstylesheets = $this->_getTemplateSytlesheetFiles($path);
			$options = array();
			
			for($i = 0; $i < count($defaultstylesheets); $i++)
			{
				$stylesheet = $defaultstylesheets[$i];
				if(!preg_match('/.*('.$this->_excludeFilter.')\./i',$stylesheet))
				{
					$stylesheetPath =  'templates'.DS.$template.DS.'css'.DS.$stylesheet;
					$stylesheet = str_replace('.css','',$stylesheet);
					$options[] =  JHTML::_('select.option', $stylesheetPath, $stylesheet);
				}	
			}	
			
			 $default = 'templates'.DS.$template.DS.'css'.DS.$default.'.css';
		}
		else
		{
			$options = array(JHTML::_('select.option', 'Automatic', 'Automatic'));
			$this->_useAutomaticOnly = true;
		}
	
	   	$list = JHTML::_('select.genericlist',  $options, 'content_css_custom', 'class="box" size="1"','value','text',$default);
		
		return $list;
	}
	
	
	public function getRichcomboStylesheetList()
	{
		
		$params = $this->_getParams();
	
		$template =  $this->_defaultTemplate;
		$default = 'Automatic';
		
		if(!$params->get('styles_css',1))
		{
			$styles_css_custom = $params->get('styles_css_custom','');
			if($styles_css_custom)
			{
				$styles_css_custom  = trim($styles_css_custom);
				if($styles_css_custom)
				{
					preg_match('/templates\/([^\/]*?)\/css\/([^\/]*).css$/',$styles_css_custom,$match);
							
					if(isset($match[1]) && $match[1] != $this->_defaultTemplate)
						$template =  $match[1];
					
					if(isset($match[2]))
						$default =  $match[2];
				}
			}
			else
			{
				$default = 'None';
			}			
				
		}
		$defaultstylesheets  = array();
		
		
		$options = array(JHTML::_('select.option', 'Automatic', 'Automatic'));
		
		$path = $this->_basepath.DS.$template.DS.'css';

		$defaultstylesheets = $this->_getTemplateSytlesheetFiles($path);
	
		if(!$this->_useAutomaticOnly)
		{
			for($i = 0; $i < count($defaultstylesheets); $i++)
			{
				$stylesheet = $defaultstylesheets[$i];
				if(!preg_match('/.*('.$this->_excludeFilter.')\./i',$stylesheet))
				{
					$stylesheetPath =  'templates'.DS.$template.DS.'css'.DS.$stylesheet;
					$stylesheet = str_replace('.css','',$stylesheet);
					$options[] =  JHTML::_('select.option', $stylesheetPath, $stylesheet);
				}	
			}
		}	
		
		if($default != 'None' && $default != 'Automatic')
			$default = 'templates'.DS.$template.DS.'css'.DS.$default.'.css';
		$options[] = JHTML::_('select.option', 'None', 'None');
		
	   	$list = JHTML::_('select.genericlist',  $options, 'styles_css_custom', 'class="box" size="1"','value','text',$default);
		
		return $list;
	}
	
	
	public function getTemplateStylesheets() 
	{
		return $this->_stylesheets;
	}
	
	public function getRichComboStylesheets() 
	{
		return $this->_comboStylesheets;
	}

	public function getBooleanList($name)
	{
	 	$params = $this->_getParams();
		
		$default = $params->get($name,'1');
		
		$options = array(
						 JHTML::_('select.option', '0', 'No'),
						 JHTML::_('select.option', '1', 'Yes')
						 );
		
		$list = JHTML::_('select.genericlist',  $options, $name, 'class="box" size="1"','value','text',$default);
		
		return $list;
	}
		
	public function store()
	{
		$post = JRequest::get('post');
		
		foreach($post as $key=>$value)
		{
			if($key == 'content_css_custom' && strtolower(trim($value)) == 'automatic')
				$post['content_css'] = 1;
			
			if($key == 'content_css_custom' && strtolower(trim($value)) != 'automatic')
				$post['content_css'] = 0;
			
			if($key == 'styles_css_custom' && strtolower(trim($value)) == 'automatic')
				$post['styles_css'] = 1;
			
			if($key == 'styles_css_custom' && strtolower(trim($value)) != 'automatic')
				$post['styles_css'] = 0;			
			
			if(strtolower(trim($value)) == 'automatic' || strtolower(trim($value)) == 'none')
				$post[$key] = '';
		}
		
		$post['editor_css'] = 0; //always disable the use of using the editor.css stylesheet;
		
		 //store JCK css typography
		$registry = $this->_getParams();
		
		if($post['jcktypography'] || !$registry->get('jcktypographycontent',false))
		{
			$cssContent = file_get_contents(JPATH_PLUGINS.'/editors/jckeditor/install/plugins/system/jcktypography.css');
			$post['jcktypographycontent'] = $cssContent;  
		}	
			
		if( defined('JLEGACY_CMS') )
			$table = JTable::getInstance('plugin');
		else
			$table = JTable::getInstance('extension');
		
	
		
	
		$registry->loadArray($post);
				
		$table->load($this->_editor->id);
		$table->params	= $registry->toString();
		
		if(!$table->store())
			return false;
		
		
		if(!$this->_installSystemPlugin())
			return false;
		
		$this->_updateConfig();

		//Delete temp manifest file if still present
		$folder = JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS;
		$file = '_jckeditor.xml';
		$path = $folder.$file;
		if(JFile::exists($path))
			JFile::delete($folder.$file); //remove all Joomla version install file 
			
		$this->updateAdminToolsHtaccess();
		
		return true;	
			
	}

	private function _installSystemPlugin()
	{
		
		
		jimport('joomla.filesystem.file');
	
		$source = JPATH_PLUGINS.'/editors/jckeditor/install/plugins/system/';
		$path = $source.'/jcktypography.xml';
		$db = JFactory::getDBO();
		
		
		if(defined('JLEGACY_CMS') )
		{
			
			$row = JTable::getInstance('plugin');
			
			$query = 'SELECT `id`' .
			' FROM `#__plugins`' .
			' WHERE folder = '.$db->Quote('system') .
			' AND element = '.$db->Quote('jcktypography');
			$db->setQuery($query);
			$id = $db->loadResult();
			
			if($id) 
			{
				
				$row->load($id);
				$row->name = 'System - JCK Typography';
				$row->published = 1;
			} 
			else 
			{
				
				$row->name = 'System - JCK Typography';
				$row->ordering = 0;
				$row->folder = 'system';
				$row->iscore = 0;
				$row->access = 0;
				$row->client_id = 0;
				$row->element = 'jcktypography';
				$row->params = '';
				$row->published = 1;
				
			}
			
				
			if (!$row->store())
			{
				return false;		
			}		
		
			//move file
			$base =  JPATH_PLUGINS.'/system';
			
			jimport('joomla.filesystem.folder');
			
			$src 	= $source.'/jcktypography.php';
			$dest 	= $base.'/jcktypography.php';
			JFile::copy( $src, $dest);
			
			$src 	= $path;
			$dest 	= $base.'/jcktypography.xml';
			JFile::copy( $src, $dest);
	
		
		}
		else
		{
			$row = JTable::getInstance('extension');
			
			jimport('joomla.application.helper');
			$xmlcache = json_encode(JApplicationHelper::parseXMLInstallFile($path));
			
					
			$query = 'SELECT `extension_id`' .
				' FROM `#__extensions`' .
				' WHERE folder = '.$db->Quote('system') .
				' AND element = '.$db->Quote('jcktypography');
			$db->setQuery($query);
			$id = $db->loadResult();
			
			if($id)
			{
				$row->load($id);
				$row->name = 'System - JCK Typography';
				$row->manifest_cache = $xmlcache;
				$row->enabled = 1;
			}
			else
			{
				$row->name = 'System - JCK Typography';
				$row->type = 'plugin';
				$row->ordering = 0;
				$row->element = 'jcktypography';
				$row->folder = 'system';
				$row->enabled = 0;
				$row->protected = 0;
				$row->access = 1;
				$row->client_id = 0;
				$row->params = '{}';
				// Custom data
				$row->custom_data = '';
				// System data
				$row->system_data = '';
				$row->manifest_cache = $xmlcache;
				$row->enabled = 1;
			}
			
			
			
			if (!$row->store())
			{
				return false;		
			}
			
					
			//move file
			$base =  JPATH_PLUGINS.'/system/jcktypography';
			
			jimport('joomla.filesystem.folder');
			
			if (!file_exists($base))
			{
				if (!JFolder::create($base))
					return false;
			}		
			
		
			$src 	= $source.'/jcktypography.php';
			$dest 	= $base.'/jcktypography.php';
			JFile::copy( $src, $dest);
			
			$src 	= $path;
			$dest 	= $base.'/jcktypography.xml';
			JFile::copy( $src, $dest);	
			
			$src 	= $source.'/index.html';
			$dest 	= $base.'/index.html';
			JFile::copy( $src, $dest);
			

		}
				
		return true;
	}
	
	private  function _updateConfig()
	{
	
		$config = JFactory::getConfig();
		$dbname = $config->get('db');
		
		$db = JFactory::getDBO();
				
		$query = "SELECT COUNT(1)
		FROM information_schema.tables 
		WHERE table_schema = '".$dbname."' 
		AND table_name = '".$db->getPrefix()."jcktoolbarplugins'";			
		
		$db->setQuery($query); 
		
		if(!$db->loadResult()) 
			return false; //bail out
		
		
		
		$query = "SELECT COUNT(p.id) AS pcount,COUNT(tp.pluginid) AS tpcount FROM #__jckplugins p
		LEFT JOIN #__jcktoolbarplugins tp on tp.pluginid = p.id
		WHERE `name` IN('html5support','video','audio','uicolor') ";

		$db->setQuery($query); 
		
		$info = $db->loadObject();
		
		if($info && $info->tpcount)
			return true;
			
		if(!$info->pcount)
		{ 
			$query = "INSERT INTO #__jckplugins (`title`,`name`,`type`,`row`,`published`,`editable`,`icon`,`iscore`,`params`, `parentid`) VALUES 
			('','html5support','plugin',0,1,1,'',1,'',NULL)";
			$db->setQuery($query);
		
			if(!$db->query())
				return false;
			
			$parentid = $db->insertid();

			$query = "INSERT INTO #__jckplugins (`title`,`name`,`type`,`row`,`published`,`editable`,`icon`,`iscore`,`params`, `parentid`) VALUES 
			('Video','video','plugin',3,1,1,'images/icon.png',1,'',".$parentid."),	
			('Audio','audio','plugin',3,1,1,'images/icon.png',1,'',".$parentid."),	
			('UIColor','uicolor','plugin',3,1,1,'uicolor.gif',1,'',NULL),
			('','imagedragndrop','plugin',0,1,1,'',1,'',NULL),
			('','ie9selectionoverride','plugin',0,1,1,'',1,'',NULL)";				
			$db->setQuery($query);
					
			if(!$db->query())
				return false;
			
			$first = $db->insertid();	
		} else
		{
			$query = 'SELECT `id` FROM #__jckplugins WHERE name = "video"';
			$db->setQuery( $query );
			$first = $db->loadResult();
		}//end if
		
		
		$last = $first+2;
		
		//get next layout row  details
		
		$query =	"SELECT row as rowid,MAX(`ordering`) +1 AS rowordering FROM #__jcktoolbarplugins WHERE toolbarid = 1 
					 GROUP BY row
					 ORDER BY row DESC LIMIT 1";
		$db->setQuery($query); 
		$rowDetail = $db->loadObject();
		
		//Only insert data in to the __jcktoolbarplugins if upgrading from old version.
		if( !$rowDetails )
		{
			return true;
		}//end if
		
		$values = array();
		
		for($i = $first; $i <= $last; $i++)
			$values[] = '(1,'. $i.','.$rowDetail->rowid.','.$rowDetail->rowordering++.',1)';
			
		$query =  "INSERT INTO #__jcktoolbarplugins(toolbarid,pluginid,row,ordering,state) VALUES "	. implode(",",$values);
	
		$db->setQuery($query);
		$db->query();
		
		
		
		jimport('joomla.filesystem.file');
			
		if($this->_overwrite)
		{
			//Get toolbar plugins object
			if( defined('JLEGACY_CMS') )
				$path = JPATH_PLUGINS.'/editors/jckeditor/includes/ckeditor';
			else
				$path = JPATH_PLUGINS.'/editors/jckeditor/jckeditor/includes/ckeditor';
			
			
			require($path.'/plugins.php');
			require($path.'/plugins/toolbarplugins.php'); 
	
			$plugins = new JCKtoolbarPlugins();
			
			foreach(get_object_vars($plugins)as $key=>$value)
			{						
				if(strpos('p'.$key,'_'))
				unset($plugins->$key);	
			}
			
			$plugins->html5support = 1;
			$plugins->video = 1;
			$plugins->audio = 1;
			$plugins->uicolor = 1;
			$plugins->imagedragndrop = 1;
			$plugins->ie9selectionoverride = 1;
			
			$config = new JRegistry('config');
			$config->loadObject($plugins);
					  

			$cfgFile = $path.'/plugins/toolbarplugins.php';
			
			// Get the config registry in PHP class format and write it to file
			if(!defined('JLEGACY_CMS'))
			{
				if(!JFile::write($cfgFile, $config->toString('PHP', array('class' => 'JCKToolbarPlugins extends JCKPlugins'))))
					return false; //if fail then bail out
			}
			else
			{
				 if (!JFile::write($cfgFile, $config->toString('PHP', 'config', array('class' => 'JCKToolbarPlugins extends JCKPlugins')))) 
				 return false; //if fail then bail out
			
			}	 
			
			require($path.'/toolbar.php');
			require($path.'/toolbar/full.php');
			
			$toolbar = new JCKFull();
					
			//fix toolbar values or they will get wiped out
			foreach (get_object_vars( $toolbar ) as $k => $v)
			{
				if(is_null($v))
				{
					$toolbar->$k = ''; 
				}
				if($k[0] == '_')
					$toolbar->$k = NULL;
			}
			
			$toolbar->Video = '';
			$toolbar->Audio = '';
			$toolbar->UIColor = '';
					
			$toolbarConfig = new JRegistry('toolbar');
			$toolbarConfig->loadObject($toolbar);	
			
			$filename = $path.'/toolbar/full.php';
			
			// Get the config registry in PHP class format and write it to file
				
			if(!defined('JLEGACY_CMS'))
			{
				if(!JFile::write($filename, $toolbarConfig->toString('PHP', array('class' => 'JCKFull extends JCKToolbar'))))
					return false;
			}
			else
			{
				if(!JFile::write($filename, $toolbarConfig->toString('PHP','toolbar', array('class' => 'JCKFull extends JCKToolbar'))))
					return false;
			}	
				
		}				
	
		return true;
	}

	private function  updateAdminToolsHtaccess()
	{
		// Define the files and folders to add to .htaccess Maker here:
		
		$base = 'plugins/editors/jckeditor/'; 
		
		$registry = null;
		
		jimport('joomla.filesystem.folder');
		
		$buffer = JFile::read(JPATH_ROOT.'/'.$base.'install/access/exceptions.ini');
		
		$chunks = explode(chr(13),trim($buffer));
	
		$folders =  array();
		$files = array();
		
		foreach($chunks as $chunk)
		{
			list($k,$v) = explode('=',$chunk);

			if($v == 'folder')
				$folders[] = $base.trim($k);
			elseif($k && $v)
				$files[] = $base.trim($k).'.'.trim($v);
		}
	
		$htmaker_additions = array(
			'folders'	=> $folders,
			'files'		=> $files
		);
	
		// DO NOT MODIFY BELOW THIS LINE

		// Is Admin Tools installed?
		if(!is_dir(JPATH_ADMINISTRATOR.'/components/com_admintools')) {
			return;
		}
		
		// Is it the Professional version?
		if(!is_file(JPATH_ADMINISTRATOR.'/components/com_admintools/models/htaccess.php') && 
		!is_file(JPATH_ADMINISTRATOR.'/components/com_admintools/models/htmaker.php') ) {
			return;
		}
		
		// Is Admin Tools enabled?
		$db = JFactory::getDbo();
	
		$query = $db->getQuery(true)
			->select($db->qn('enabled'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('com_admintools'))
			->where($db->qn('type').' = '.$db->q('component'));
		$db->setQuery($query);

		$enabled = $db->loadResult();
		if(!$enabled) return;

		// Do we have a custom .htaccess file?
		$generateHtaccess = false;
		jimport('joomla.filesystem.file');
		$htaccess = JFile::read(JPATH_ROOT.'/.htaccess');
		if($htaccess !== false) {
			$htaccess = explode("\n", $htaccess);
			if($htaccess[1] == '### Security Enhanced & Highly Optimized .htaccess File for Joomla!') {
				$generateHtaccess = true;
			}
		}

		// Load the FoF library
		if(!defined('FOF_INCLUDED')) {
			include_once JPATH_LIBRARIES.'/fof/include.php';
		}

		// Load the .htaccess Maker configuration
		if(!class_exists('AdmintoolsModelStorage')) {
			include_once JPATH_ADMINISTRATOR.'/components/com_admintools/models/storage.php';
		}
		$model = FOFModel::getTmpInstance('Htmaker','AdmintoolsModel');
		$config = $model->loadConfiguration();

		if(is_string($config->exceptionfiles)) {
			$config->exceptionfiles = explode("\n", $config->exceptionfiles);
		}
		if(is_string($config->exceptiondirs)) {
			$config->exceptiondirs = explode("\n", $config->exceptiondirs);
		}

		// Initialise
		$madeChanges = false;

		// Add missing files
		if(!empty($htmaker_additions['files'])) {
			foreach($htmaker_additions['files'] as $f) {
				if(!in_array($f, $config->exceptionfiles)) {
					$config->exceptionfiles[] = $f;
					$madeChanges = true;
				}
			}
		}

		// Add missing folders
		if(!empty($htmaker_additions['folders'])) {
			foreach($htmaker_additions['folders'] as $f) {
				if(!in_array($f, $config->exceptiondirs)) {
					$config->exceptiondirs[] = $f;
					$madeChanges = true;
				}
			}
		}

		if($madeChanges) {
			// Save the configuration
			
			$customhead =  $config->custhead;
			if(!strpos($customhead,'pixlr.com'))
				$customhead .= "\nRewriteCond %{QUERY_STRING} image=http://[a-zA-Z0-9_]+\.pixlr.com
RewriteRule .* - [L]";
			
			$updates = array(
				'exceptionfiles' => implode("\n", $config->exceptionfiles),
				'exceptiondirs' => implode("\n", $config->exceptiondirs),
				'custhead'=> $customhead
			);

			$model->saveConfiguration($updates);
			if($generateHtaccess) {
				$model->writeHtaccess();
			}
		}
	}
	
}
