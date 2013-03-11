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
JHtml::_('behavior.multiselect');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<?php if(!empty( $this->sidebar)): ?>
	<div id="sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="main-container" class="span10">
<?php else : ?>
	<div id="main-container">
<?php endif;?>
		<?php if ($this->ftp) : ?>
			<?php echo $this->loadTemplate('ftp'); ?>
		<?php endif; ?>
		<?php if (count($this->items)) : ?>
		<table class="table table-striped adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="20" class="nowrap center hidden-phone"><?php echo JText::_( 'Num' ); ?></th>
					<th width="1%">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th class="nowrap"><?php echo JText::_( 'Plugin' ); ?></th>
					<th class="nowrap center" width="10%"><?php echo JText::_( 'Version' ); ?></th>
					<th class="nowrap hidden-phone" width="15%"><?php echo JText::_( 'Date' ); ?></th>
					<th class="nowrap hidden-phone" width="25%"><?php echo JText::_( 'Author' ); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach($this->items as $i => $item ) : ?>
				<?php
					$this->loadItem($i);
					echo $this->loadTemplate('item');
				?>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
			<table class="table table-striped" cellspacing="1">
				<tr>
					<td class="nowrap center"><?php echo JText::_( 'No custom plugins' ); ?></td>
				</tr>
			</table>
		<?php endif; ?>

		<input type="hidden" name="task" value="manage" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="option" value="com_jckman" />
		<input type="hidden" name="view" value="plugin" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</div>
</form>