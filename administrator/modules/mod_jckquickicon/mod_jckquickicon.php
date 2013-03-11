<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

if (!defined( '_JCK_QUICKICON_MODULE' ))
{
	/** ensure that functions are declared only once */
	define( '_JCK_QUICKICON_MODULE', 1 );

	require_once( JPATH_COMPONENT .DS. 'helper.php' );

	function quickiconButton( $link, $image, $text, $id, $path = false,$modalclass='' )
	{
		$app 		= JFactory::getApplication();
		$lang		= JFactory::getLanguage();
		$template	= $app->getTemplate();
		
		if( !$path )
		{
			$path = 'templates/'. $template .'/images/header/';
		}//end if
        
        $modalref = ($modalclass ? ' class="'.$modalclass.'"  rel="{handler: \'iframe\' , size: {x:571, y:400}}"' : ''); 
        
       if($modalref)
       {
            if(!defined('ADD_MODAL_CLASS')) //only do this once
            {
                $doc = JFactory::getDocument();
                $doc->addScriptDeclaration(
               "window.addEvent('domready', function()
			   {
                    $$('a.modal').each(function(el)
                    {
                        el.addEvent('click', function()
                        {
                            (function()
                            {
                                SqueezeBox.overlay.removeEvent('click',SqueezeBox.bound.close);
                            }).delay(250);
                        }); 
                    }); 
               });"); 
               define('ADD_MODAL_CLASS',1);   
            }    
        }
		// RENDER BTN
		// the id is for auto firing of the buttons
		?>
		<a id="jcktaskbtn_<?php echo $id; ?>" href="<?php echo $link; ?>"<?php echo $modalref;?>>
			<?php echo JHTML::image( JUri::root() . $path . $image, $text ); ?>
			<div><?php echo $text; ?></div>
		</a>
		<?php
	}

	echo '<div id="jckcpanel">';
	
	$size = '64';
	$base = 'index.php?option=com_jckman';
	$view = '&amp;view=';
	$task = '&amp;task=';
	$path = 'administrator/components/com_jckman/icons/';
	$canDo = JCKHelper::getActions();
	$isMobile = JCKHelper::isMobile();
	$isIOS =   (JCKHelper::isMobile() || JCKHelper::isiPad());

	quickiconButton( $base . $view . 'list', 'icon-' . $size . '-plugin.png', JText::_( 'Plugin Manager' ), 'list', $path );

	if( $canDo->get('jckman.install') )
	{
		quickiconButton( $base . $view . 'install', 'icon-' . $size . '-installer.png', JText::_( 'Installer' ), 'install', $path );
	}

	if( $canDo->get('jckman.uninstall') )
	{
		quickiconButton( $base . $view . 'plugin', 'icon-' . $size . '-uninstaller.png', JText::_( 'Uninstaller' ), 'plugin', $path );
	}

	$jckinstallerpath = JPATH_PLUGINS.DS.'editors'.DS.'jckeditor'.DS.'install'.DS;

	if( $canDo->get('core.edit') )
	{
		if(is_dir($jckinstallerpath))
		{
			$link = JURI::root() . 'plugins/editors/jckeditor/install/index.php?task=permissions';
			quickiconButton( $link, 'icon-' . $size . '-systemcheck.png', JText::_( 'System Check' ), 'system', $path, 'modal');
		}
		else
		{
			quickiconButton( $base . $task . 'cpanel.check', 'icon-' . $size . '-systemcheck.png', JText::_( 'System Check' ), 'system', $path );
		}
	}
	if(!$isIOS)
		quickiconButton( $base . $view . 'toolbars', 'icon-' . $size . '-layout.png', JText::_( 'Layout Manager' ), 'toolbars', $path );

	if( $canDo->get('core.edit') )
	{
		if(!$isIOS) 
			quickiconButton( $base . $view . 'import', 'icon-' . $size . '-import.png', JText::_( 'Restore' ), 'import', $path );
	}

	if(!$isIOS) 
		quickiconButton( $base . $task . 'cpanel.export', 'icon-' . $size . '-export.png', JText::_( 'Backup' ), 'export', $path );

	$db = JFactory::getDBO();
	$db->setQuery('SELECT extension_id  FROM #__extensions WHERE type = "plugin" AND folder= "editors" AND element = "jckeditor"');
	$result = $db->loadresult();

	if($result)
	{
		if( $canDo->get('jckman.sync') )
		{
			quickiconButton( $base . $task . 'cpanel.sync', 'icon-' . $size . '-sync.png', JText::_( 'Sync' ), 'sync', $path );
		}

		$link = 'index.php?option=com_plugins&amp;task=plugin.edit&amp;extension_id='.$result;
		quickiconButton( $link, 'icon-' . $size . '-editor.png', JText::_( 'JCK Editor' ), 'editor', $path );
	}

	echo '</div>';
}