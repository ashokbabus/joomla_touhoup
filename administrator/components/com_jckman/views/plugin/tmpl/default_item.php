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

?>
<tr>
	<td class="center hidden-phone">
		<?php echo $this->pagination->getRowOffset( $this->item->index ); ?>
	</td>
	<td class="center">
		<?php echo JHtml::_('grid.id', $i, $this->item->id); ?>
	</td>
	<td class="jckbreak">
		<?php echo $this->item->name;?>
	</td>
	<td class="center">
		<?php echo @$this->item->version ? $this->item->version : '--'; ?>
	</td>
	<td class="hidden-phone">
		<?php echo @$this->item->creationdate ? $this->item->creationdate : '--'; ?>
	</td>
	<td class="hidden-phone">
		<span class="hasTip" title="<?php echo JText::_( 'Author Information' );?>::<?php echo $this->item->author_info; ?>">
			<?php echo @$this->item->author ? $this->item->author : '--'; ?>
		</span>
	</td>
</tr>
