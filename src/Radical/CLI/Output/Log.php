<?php
namespace Radical\CLI\Output;

class Log {
	const LOG_PATH = '/var/log/radical/';
	const LOG_EXT = '.log';
	
	static $main;
	static $group;
	
	static function init($name){		
		self::$main = self::Create($name);
	}
	static function getPath(){
		global $BASEPATH;
		$path = $BASEPATH.'/'.self::LOG_PATH.'/';
		@mkdir($path);
		return $path;
	}
	static function createGroup($name){
		self::$group = $name;
		$file = static::getPath().self::$group.'/';
		if(!file_exists($file)){
			@mkdir($file);
		}
		self::$main = self::Create();
	}
	static function create($name = 'main'){
		$file = static::getPath();
		if(self::$group){
			$file .= self::$group.'/';
		}
		$file .= $name;
		$file .= self::LOG_EXT;
		
		$ret = new LogFile($file);
		return $ret;
	}
	
	static function GET(){
		if(!self::$main){
			return self::Create();
		}
		return self::$main;
	}
	
	static function end(){
		self::$group = null;
	}
}