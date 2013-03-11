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
define('JCK_COMPONENT_VIEW', JCK_COMPONENT. '/views/list');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$sortFields = $this->getSortFields();

//load style sheet
$document = JFactory::getDocument();
$document->addStyleSheet( JCK_COMPONENT . '/css/icons.css', 'text/css' );
$document->addStyleSheet( JCK_COMPONENT_VIEW . '/css/plugins.css', 'text/css' );

?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_jckman'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty( $this->sidebar)): ?>
	<div id="sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="main-container" class="span10">
<?php else : ?>
	<div id="main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo 'Search in plugin title.'; ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo 'Search in plugin title.'; ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn" rel="tooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn" rel="tooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		<div class="clr">&nbsp;</div>
		<table class="table table-striped adminlist">
			<thead>
				<tr>
					<th width="20" class="nowrap center hidden-phone">
						<?php echo JText::_( 'Num' ); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th width="5%" class="nowrap center">
						<?php echo JHTML::_('grid.sort', JText::_('JSTATUS'), 'p.published', $listDirn, $listOrder ); ?>
					</th>
					<th class="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('JGLOBAL_TITLE'), 'p.title', $listDirn, $listOrder ); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'Name', 'p.name', $listDirn, $listOrder ); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', 'Icon', 'p.icon', $listDirn, $listOrder ); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHTML::_('grid.sort', JText::_('JGRID_HEADING_ID'), 'p.id', $listDirn, $listOrder ); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="7">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'p.type');
					$canCreate  = $this->user->authorise('core.create',     'com_jckman');
					$canEdit    = $this->user->authorise('core.edit',       'com_jckman') && $item->editable;
					$canCheckin = $this->user->authorise('core.manage',     'com_checkin') || $item->checked_out == $this->user->get('id')|| $item->checked_out == 0;
					$canChange  = $this->user->authorise('core.edit.state', 'com_jckman') && $canCheckin;
					$title		= ( $item->title ) ? $item->title : $item->name;
				?>
				<tr>
					<td class="center hidden-phone">
						<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'list.', $canChange, 'cb'); ?>
					</td>
					<td class="has-context">
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'list.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit && $canCheckin) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_jckman&task=list.edit&cid[]='.(int) $item->id); ?>">
									<?php echo $this->escape($title); ?></a>
							<?php else : ?>
									<?php echo $this->escape($title); ?>
							<?php endif; ?>
							<div class="small">Core Plugin: <?php echo ($item->iscore) ? JText::_( 'JYES' ) : JText::_( 'JNO' ); ?></div>
						</div>
						<div class="pull-left">
							<?php
								// Create dropdown items
								if ($canEdit) :
									JHtml::_('dropdown.edit', $item->id . '&cid[]=' . $item->id, 'list.');
									JHtml::_('dropdown.divider');
								endif;

								if( $canChange ) :
									if ($item->published) :
										JHtml::_('dropdown.unpublish', 'cb' . $i, 'list.');
									else :
										JHtml::_('dropdown.publish', 'cb' . $i, 'list.');
									endif;
								endif;

								if ($item->checked_out && $canCheckin) :
									JHtml::_('dropdown.divider');
									JHtml::_('dropdown.checkin', 'cb' . $i, 'list.');
								endif;

								// Render dropdown list
								echo JHtml::_('dropdown.render');
							?>
						</div>
					</td>
					<td class="hidden-phone">
						<?php echo $item->name;?>
					</td>
					<td class="hidden-phone">
						<?php
							if( $item->icon && is_numeric($item->icon))
							{
								echo '<img  src="'. JCK_COMPONENT_VIEW .'/images/spacer.gif" alt="' . $item->name .'" class="cke_icon"  style="background-position:0px ' . $item->icon  .'px;"/>';	
							}
							elseif($item->icon)
							{
								echo '<img src="../plugins/editors/jckeditor/plugins/' . $item->name .'/'.$item->icon.'" alt="'. $item->name .'" />';	
							}
							else
							{
								echo $item->name;
							}
						?>
					</td>
					<td class="center hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="type" value="list" />
		<input type="hidden" name="view" value="list" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</div>
</form>