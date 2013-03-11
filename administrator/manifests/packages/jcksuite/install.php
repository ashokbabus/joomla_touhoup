<?php

defined('_JEXEC') or die('Restricted access');

// -PC- J3.0 fix
if( !defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

class pkg_jcksuiteInstallerScript
{
	
	function 	preflight( $type, $parent ) 
	{
		$jversion = new JVersion();
		 // Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
  
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) 
		{
			Jerror::raiseWarning(null, 'Cannot install this version of the JCK Suite package in a Joomla release prior to '.$this->minimum_joomla_release);
			return false;
		}
	}
		
	function postflight($parent)
	{
		?>
		<a id="jckmodal-install" href="../plugins/editors/jckeditor/install/index.php" rel="{handler: 'iframe' , size: {x:571, y:400}}" title="test" style="visibility:hidden">test</a>
		<style type="text/css">
			#sbox-btn-close { display:none;}
			#sbox-window{ background-color : #000000;}
		</style>
		<script type="text/javascript">
		if (typeof SqueezeBox == "undefined") 
		{
				 var head = document.getElementsByTagName('head')[0];
				 var link = document.createElement('link');
			 	 link.type = 'text/css';
				 link.rel = 'stylesheet';
				 link.href = '../media/system/css/modal.css';
				 head.appendChild(link);
				
				var script = document.createElement('script');
				script.type= 'text/javascript';
				script.src= '../media/system/js/modal.js';
				head.appendChild(script);
			
			if(script && script.onreadystatechange)
			{
				script.onreadystatechange = function() 
				{
				   if (this.readyState == 'complete')
				   {
						if($$('#system-message dd.error ul').length < 1) //check to see if there are no errors reported
						{
							var wizard = document.getElementById("jckmodal-install");
							SqueezeBox.fromElement(wizard,	{ parse: 'rel'});
							(function()
							{
								SqueezeBox.bound  &&  SqueezeBox.overlay.removeEvent('click',SqueezeBox.bound.close) || SqueezeBox.overlay.removeEvent('click',SqueezeBox.listeners.close);
							}).delay(250);	
						}	
				   }	
				};
			}
			else
			{
				if(script)
				{		
					script.onload =  function()
					{
						
						if($$('#system-message dd.error ul').length < 1) //check to see if there are no errors reported
						{
							var wizard = document.getElementById("jckmodal-install");
							SqueezeBox.fromElement(wizard,	{ parse: 'rel'});
							(function()
							{
								SqueezeBox.bound  &&  SqueezeBox.overlay.removeEvent('click',SqueezeBox.bound.close) || SqueezeBox.overlay.removeEvent('click',SqueezeBox.listeners.close);
							}).delay(250);	
						}
					}
				}
			}
		}
		else
		{
			if($$('#system-message dd.error ul').length < 1) //check to see if there are no errors reported
				{
					var wizard = document.getElementById("jckmodal-install");
					SqueezeBox.fromElement(wizard,	{ parse: 'rel'});
					(function()
					{
						SqueezeBox.bound  &&  SqueezeBox.overlay.removeEvent('click',SqueezeBox.bound.close) || SqueezeBox.overlay.removeEvent('click',SqueezeBox.listeners.close);
					}).delay(250);	
				}
		}	
		</script>
		<?php
	}
}