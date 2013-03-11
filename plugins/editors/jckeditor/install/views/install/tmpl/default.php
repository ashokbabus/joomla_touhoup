<?php 
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 
defined('JPATH_BASE') or die;

$os = strtoupper(substr(PHP_OS, 0, 3));
$isWin = ($os === 'WIN');

$user = JFactory::getUser();

?>
<style>
a:link, a:visited,  a:active, a:hover   {text-decoration: none;}
</style>

</style>
<script src="../jscolor/jscolor.js" type="text/javascript"></script>
<script src="js/combobox.js" type="text/javascript"></script>
<script type="text/javascript">
function submitform(pressbutton){
	if (pressbutton) {
		document.adminForm.task.value=pressbutton;
	}
	if (typeof document.adminForm.onsubmit == "function") {
		document.adminForm.onsubmit();
	}
	document.adminForm.submit();
}

var Joomla = {};

Joomla.submitbutton = function(pressbutton) {
	submitform(pressbutton);
}
</script>

<div class="header">
	<div class="innerHeader">
	<img src="images/headerTitle.png" height="48" width="155" />
	</div>
</div>
<form name="adminForm" id="adminForm" action="index.php" method="post">
<div class="container">	
	<div class="mainBody">
		<h3>Wecome <?php echo $user->name; ?></h3>
		<fieldset class="adminform">
		<legend>Getting Started</legend>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td style="padding-right:10px;"><a href="http://www.joomlackeditor.com/installation-guide?start=3#setup_wizard" target="_blank"><img align="middle"  border="0" src="images/installguide.png"/></a></td>
			<td><p>Getting off on the right foot can make all the difference to your editing experience! That's why we have taken the time to develop a free 'Installation Guide' to support you through the installation of this extension. </p>

<p>If you haven't done so already we would advise you to click on the Installation Guide link below before you go any further. This will help you get the best out of the editor and walk you through, the latest changes, setup and site configuration. </p>

<strong>Download:</strong>&nbsp; <a href="http://www.joomlackeditor.com/installation-guide?start=3#setup_wizard" target="_blank">Installation Guide...</a></td>			
		</tr>
		</table>
		</fieldset>
	</div>
</div>
<div class="buttons">
<input type="submit" value="Next  >>>" />
</div>	
<input type="hidden" name="task" value="permissions" />
 </form>