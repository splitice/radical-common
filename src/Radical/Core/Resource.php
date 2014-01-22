<?php
namespace Radical\Core;

class Resource {
	private $path;
	function __construct($path){
		$this->path = $path;
	}
	
	private $fullPath;
	function getFullPath(){
		if($this->fullPath) return $this->fullPath;
		
		global $BASEPATH;
		
		$file = $BASEPATH.$this->path;
		if(file_exists($file)){
			$this->fullPath = $file;
			return $file;
		}
	}
	function getFiles($expr = '*'){
		global $BASEPATH;
		
		$files = array();
		$file = $BASEPATH.$this->path;
		if(file_exists($file) && is_dir($file)){
			$files = array_merge($files,glob($file.DIRECTORY_SEPARATOR.$expr));
		}
		
		return $files;
	}
	function exists(){
		global $BASEPATH;
	
		$file = $BASEPATH.$this->path;
		if(file_exists($file) && is_dir($file)){
			return true;
		}
	
		return false;
	}
}