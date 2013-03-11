<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// no direct access
defined( '_JEXEC' ) or die();

JHTML::_('behavior.tooltip');
JHTML::_('behavior.framework');
JHTML::_('behavior.modal');

define('JCK_COMPONENT_VIEW', JUri::root() . 'administrator/components/com_jckman/views/cpanel');
//load style sheet
$document = JFactory::getDocument();
$document->addStyleSheet( JCK_COMPONENT_VIEW . '/css/cpanel.css', 'text/css' );

$task = $this->app->input->get( 'taskbtn', false );

if( $task )
{
	// Manually fire off a button (safari/chrome do not support .click() )
	$js = 'window.addEvent( "load", function( ev )
			{
				var x = document.id( "jcktaskbtn_' . $task . '" );

				if( x && x.click )
				{
					x.click();
				}
				else if( x )
				{
					fireTheEvent( x, "click");
				}
			})

			function fireTheEvent(element,event)
			{
				if (document.createEventObject)
				{
					// dispatch for IE
					var evt = document.createEventObject();
					return element.fireEvent("on"+event,evt)
				}
				else
				{
					var evt = document.createEvent("HTMLEvents");
					evt.initEvent(event, true, true ); // event type,bubbling,cancelable
					return !element.dispatchEvent(evt);
				}
			}
			';
	$document->addScriptDeclaration( $js );
}//end if
?>
<?php if(!empty( $this->sidebar)): ?>
	<div id="sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="main-container" class="span10">
<?php else : ?>
	<div id="main-container">
<?php endif;?>
	<div class="span6">
		<?php
			foreach ($this->icons as $i => $module)
			{
				$title = ( $i ) ? '<div class="module-title nav-header">' . $module->title . '</div>' : '';
				$class = ( $module->module == 'mod_jckquickicon' ) ? 'row-striped' : '';
				echo '<div class="well well-small">';
				echo $title;
				echo '<div class="' . $class . '">';
				echo JCKModuleHelper::renderModule( $module );
				echo '</div>';
				echo '</div>';
			}
		?>
	</div>
	<div class="well well-small span6">
		<div class="module-title nav-header"><?php echo 'FAQs'; ?></div>
		<div class="row-striped">
			<?php
				$first = current( $this->modules );
				echo JHtml::_( 'bootstrap.startAccordion', 'JCKSliders', array( 'active' => 'slider-' . $first->id ) );

				foreach ($this->modules as $i => $module)
				{
					$title 		= $module->title;
					$subtitle 	= chr( 32 ) . '<span>FAQs</span>';
					echo '<div class="row-fluid">';
					echo JHtml::_( 'bootstrap.addSlide', 'JCKSliders', $title . $subtitle, 'slider-' . $module->id );
					echo JCKModuleHelper::renderModule( $module );
					echo JHtml::_( 'bootstrap.endSlide' );
					echo '</div>';
				}

				echo JHtml::_( 'bootstrap.endAccordion' );
			?>
		</div>
	</div>
</div>