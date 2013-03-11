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

JHtml::_('formbehavior.chosen', 'select');

JHtml::script( JURI::root() . 'administrator' . DS . 'components' . DS . $this->app->input->get( 'option', 'com_jckman' ) . DS . 'js' . DS . 'toolbars.js' );

?>
<script language="javascript" type="text/javascript">
	function getForm()
	{
		return document.getElementById('adminForm');
	}

	Joomla.submitbutton = function(pressbutton) {
		var form = getForm();

		// do field validation
		if (form.install_package.value == ""){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_PACKAGE', true); ?>");
		} else {
			form.installtype.value = 'upload';
			form.submit();
		}
	}

	Joomla.submitbutton3 = function(pressbutton) {
		var form = getForm();

		// do field validation
		if (form.install_directory.value == ""){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_PLEASE_SELECT_A_DIRECTORY', true); ?>");
		} else {
			form.installtype.value = 'folder';
			form.submit();
		}
	}

	Joomla.submitbutton4 = function(pressbutton) {
		var form = getForm();

		// do field validation
		if (form.install_url.value == "" || form.install_url.value == "http://"){
			alert("<?php echo JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL', true); ?>");
		} else {
			form.installtype.value = 'url';
			form.submit();
		}
	}

	// Fire off pre-selections
	window.addEvent( 'domready', function( ev )
	{
		allselections();
	});

</script>
<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal">
<?php if(!empty( $this->sidebar)): ?>
	<div id="sidebar-container" class="span2 <?php echo 'jckman_' . $this->app->input->get( 'view' ); /* accessibility tmpl fix */ ?>">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="main-container" class="span10">
<?php else : ?>
	<div id="main-container">
<?php endif;?>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#upload" data-toggle="tab"><?php echo JText::_('COM_INSTALLER_UPLOAD_PACKAGE_FILE'); ?></a></li>
			<li><a href="#directory" data-toggle="tab"><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_DIRECTORY'); ?></a></li>
			<li><a href="#url" data-toggle="tab"><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_URL'); ?></a></li>
			<?php if ($this->ftp) : ?>
				<li><a href="#ftp" data-toggle="tab"><?php echo JText::_('COM_INSTALLER_INSTALL_FTP'); ?></a></li>
			<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="upload">
				<legend><?php echo JText::_('COM_INSTALLER_UPLOAD_PACKAGE_FILE'); ?></legend>
				<div class="control-group">
					<label for="install_package" class="control-label"><?php echo JText::_('COM_INSTALLER_PACKAGE_FILE'); ?></label>
					<div class="controls">
						<input id="install_package" name="install_package" class="span6 input_box" type="file" size="57" />
					</div>
				</div>
				<div class="form-actions">
					<input class="btn btn-primary" type="button" value="<?php echo JText::_('COM_INSTALLER_UPLOAD_AND_INSTALL'); ?>" onclick="Joomla.submitbutton()" />
				</div>
			</div>
			<div class="tab-pane" id="directory">
				<legend><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_DIRECTORY'); ?></legend>
				<div class="control-group">
					<label for="install_directory" class="control-label"><?php echo JText::_('COM_INSTALLER_INSTALL_DIRECTORY'); ?></label>
					<div class="controls">
						<input type="text" id="install_directory" name="install_directory" class="span6 input_box" value="<?php echo $this->state->get('install.directory'); ?>" />
					</div>
				</div>
				<div class="form-actions">
					<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton3()" />
				</div>
			</div>
			<div class="tab-pane" id="url">
				<legend><?php echo JText::_('COM_INSTALLER_INSTALL_FROM_URL'); ?></legend>
				<div class="control-group">
					<label for="install_url" class="control-label"><?php echo JText::_('COM_INSTALLER_INSTALL_URL'); ?></label>
					<div class="controls">
						<input type="text" id="install_url" name="install_url" class="span6 input_box" value="http://" />
					</div>
				</div>
				<div class="form-actions">
					<input type="button" class="btn btn-primary" value="<?php echo JText::_('COM_INSTALLER_INSTALL_BUTTON'); ?>" onclick="Joomla.submitbutton4()" />
				</div>
			</div>
			<?php if ($this->ftp) : ?>
			<div class="tab-pane" id="ftp">
				<?php echo $this->loadTemplate('ftp'); ?>
			</div>
			<?php endif; ?>
		</div>
		<div>
			<legend><?php echo JText::_('Select toolbar to install plugin'); ?></legend>
			<?php foreach ($this->form->getFieldset('toolbars') as $field) : ?>
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
	</div>
	<input type="hidden" name="view" value="install" />
	<input type="hidden" name="type" value="" />
	<input type="hidden" name="installtype" value="upload" />
	<input type="hidden" name="task" value="install.install" />
	<input type="hidden" name="option" value="com_jckman" />
	<input type="hidden" name="controller" value="Install" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>