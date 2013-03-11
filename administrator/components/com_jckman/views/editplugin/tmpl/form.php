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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::script( JURI::root() . 'administrator' . DS . 'components' . DS . $this->app->input->get( 'option', 'com_jckman' ) . DS . 'js' . DS . 'toolbars.js' );
JHtml::script( JURI::root() . 'administrator' . DS . 'components' . DS . $this->app->input->get( 'option', 'com_jckman' ) . DS . 'js' . DS . 'groups.js' );

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'list.cancel' || document.formvalidator.isValid(document.id('style-form'))) {
			Joomla.submitform(task, document.getElementById('style-form'));
		}
	}

	// Fire off pre-selections
	window.addEvent( 'domready', function( ev )
	{
		<?php if($this->item->toolbars == 'all') : ?>
			allselections();
		<?php elseif($this->item->toolbars == 'none') : ?>
			disableselections();
		<?php endif; ?>

		<?php if($this->item->group == 'all') : ?>
			allgroups();
		<?php elseif($this->item->group == 'special') : ?>
			disablegroups();
		<?php endif; ?>
	});
</script>
<form action="<?php echo JRoute::_('index.php?option=com_jckman&task=list.edit&cid='.(int)$this->item->id); ?>" method="post" name="adminForm" id="style-form" class="form-validate form-horizontal">
<?php if(!empty( $this->sidebar)): ?>
	<div id="sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		<?php JCKHelper::fixBug(); ?>
	</div>
	<div id="main-container" class="span10">
<?php else : ?>
	<div id="main-container">
<?php endif;?>
		<fieldset class="adminform">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS');?></a></li>
				<?php if( $this->item->title ) : ?>
					<li><a href="#editor" data-toggle="tab"><?php echo JText::_('Editor Options');?></a></li>
				<?php endif; ?>
				<li><a href="#options" data-toggle="tab"><?php echo JText::_('COM_PLUGINS_BASIC_FIELDSET_LABEL');?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="details">
					<?php foreach ($this->form->getFieldset('general') as $field) : ?>
						<div class="control-group">
							<div class="control-label">
								<?php echo $field->label; ?>
							</div>
							<div class="controls">
								<?php echo $field->input; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<div class="tab-pane" id="editor">
					<?php echo $this->loadTemplate('editor'); ?>
				</div>
				<div class="tab-pane" id="options">
					<?php echo $this->loadTemplate('options'); ?>
				</div>
			</div>
		</fieldset>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<?php echo JHtml::_('form.token'); ?>