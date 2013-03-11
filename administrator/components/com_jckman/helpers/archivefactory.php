<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;

class ArchiveFactory
{

	private  $phar = null;
	private  $baseDir = '';
    private  $name = '';	
	private $archive = null;
	
	public  function __construct($baseDir,$name)
	{
		$this->baseDir = $baseDir;
		$this->name = $name;
	}
   
    private function build()
   {
		if(class_exists('PharData'))
			$this->pharBuild();
		else
			$this->zipBuild();
	}
	
	private function pharBuild()
	{
		$this->type = $type = 'tar.gz';
		$this->phar = new PharData($this->name.'.tar');
		$this->phar->buildFromDirectory($this->baseDir);
		$this->phar->convertToData(Phar::TAR,Phar::GZ,'.'.$this->type);
		unset( $this->phar ); // remove any references to this file!
		unlink($this->name .'.tar');
		$this->archive =  file_get_contents($this->name.'.'.$this->type);
		unlink($this->name. '.'.$this->type);
	
	}
	
	
	private function zipBuild()
	{
		$this->type = $type = 'zip';
		
		$zip = new ZipArchive();
		// open archive 
		
		$name = $this->name.'.zip';
		if ($zip->open($name, ZIPARCHIVE::CREATE) !== TRUE) 
			throw new Exception ("Could not open archive");
		
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->baseDir,FilesystemIterator::SKIP_DOTS));
		
		foreach ($iterator as $key=>$value)
		{	
			 $filterKey = str_replace($this->baseDir.'/','',$key);
			
			if(!$zip->addFile(realpath($key),$filterKey)) 
				throw new Exception('ERROR: Could not add file: '.$filterKey);
		}
		$zip->close();		
		$this->archive =  file_get_contents($name);
		unlink($name);
	}
	
   
    public function downloadFile()
	{
		 $this->build();

		 switch ($this->options['type'])
		{
			case "tar.gz":
			case "tgz":
				header("Content-Type: application/x-gzip");
				break;
			case "bzip":
				header("Content-Type: application/x-bzip2");
				break;
			case "zip":
				header("Content-Type: application/zip");
				break;
			case "tar":
				header("Content-Type: application/x-tar");
		}
		$name =  $this->name. '.'. $this->type;
		
		$header = "Content-Disposition: attachment; filename=\"";
		$header .= $name;
		$header .= "\"";
		header($header);
		header("Content-Length: " . strlen($this->archive));
		header("Content-Transfer-Encoding: binary");
		header("Cache-Control: no-cache, must-revalidate, max-age=60");
		header("Expires: Sat, 01 Jan 2000 12:00:00 GMT");
		echo $this->archive;
		exit;
	}
}