<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
defined('JPATH_BASE') or die;

jimport( 'joomla.utilities.utility' );

if (!function_exists('class_alias')) {
    function class_alias($original, $alias) {
        eval('abstract class ' . $alias . ' extends ' . $original . ' {}');
    }
}

class JCKLoader
{
	static public function loadExtendClass($type)
	{
		$classname = 'JCK'.ucfirst($type);
		if(class_exists($classname))
			return;
		
		$legacyClass = 'J'.ucfirst($type).'Legacy';	
		$alias = 'JCK'.ucfirst($type);
		if(!class_exists($legacyClass))	
			class_alias('J'.$type,$alias);
		else 
			class_alias($legacyClass, $alias);  	
	
	}

}

JCKLoader::loadExtendClass('controller');

class InstallController extends JCKController
{

	
	private $_overwrite = true;
		
	public static function isWinOS()
	{
		$os = strtoupper(substr(PHP_OS, 0, 3));
		$isWin = ($os === 'WIN');
		return $isWin;
	}
			
	public function __construct( $config = array() )
	{
	
		if(defined('JLEGACY_CMS'))
		{
			$user = & JFactory::getUser();
			if (!$user->authorize('com_installer', 'installer')) {
				echo JError::raiseError( 404, JText::_('ALERTNOTAUTH') );
				exit();
			}
		
		}
		else
		{
			$user =  JFactory::getUser();
			if (!$user->authorise('core.manage', 'com_installer')) {
				echo JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				exit();
			}
		}
	
		parent::__construct($config);
	}
	
	public function display($cachable = false, $urlparams = false)
	{
		JRequest::setVar('view','install');
		parent::display($cachable ,$urlparams );
	}
	
	public function permissions($cachable = false, $urlparams = false)
	{
		
		if(self::isWinOS())
		{	
			//skip permisson screen
			JRequest::setVar('view','install');
			JRequest::setVar('layout','default2');
		}
		else
		{
			JRequest::setVar('view','install');
			JRequest::setVar('layout','default1');
		}			
		parent::display();
	}
	
	
	
	public function font()
	{
		JRequest::setVar('view','install');
		JRequest::setVar('layout','default2');
		parent::display();
	}

	public function folders()
	{
		$model = $this->getModel('font');
		$model->store();
		JRequest::setVar('view','install');
		JRequest::setVar('layout','default3');
		parent::display();
	}

	public function template()
	{
		
		$model = $this->getModel('folders');
		$model->store();
		JRequest::setVar('view','install');
		JRequest::setVar('layout','default4');
		parent::display();
	}
	
	
	public function finish()
	{
		$model = $this->getModel('template');
		$model->store();
		echo '<script type="text/javascript">window.parent.SqueezeBox.close();</script>';
	}
	
		
	public function changeexecutablepermission()
	{
		$model = $this->getModel("install");
		$model->changeExecutablePermission();
		$app = JFactory::getApplication();
		$this->setRedirect('index.php');
	}
	
	public function changefilespermission()
	{
		$model = $this->getModel("install");
		$model->changeFilesPermission();
		$app = JFactory::getApplication();
		$this->setRedirect('index.php');
	
	}
	
	public function changefolderspermission()
	{
		
		$model = $this->getModel("install");
		$model->changeFoldersPermission();
		$app = JFactory::getApplication();
		$this->setRedirect('index.php');
	
	}
	
	public function changeimagefolderswritablepermission()
	{
		
		$model = $this->getModel("install");
		$model->changeImageFoldersWritablePermission();
		$app = JFactory::getApplication();
		$this->setRedirect('index.php');
	}

}