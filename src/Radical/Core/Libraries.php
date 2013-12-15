<?php
namespace Radical\Core;

class Libraries {
	/**
	 * Resolves a relative path to class the
	 * appropriate full class path.
	 * 
	 * @param string $path partial path to class
	 * @return string
	 */
	static function path($path){
		return \Autoloader::resolve($path);
	}
	
	/**
	 * Convert path slashes into namespace seperators
	 * 
	 * @param string $path
	 * @return string
	 */
	static function toClass($path){
		return str_replace(DIRECTORY_SEPARATOR, '\\', $path);
	}
	
	/**
	 * Convert a class to path format, optionally resolving
	 * 
	 * @param string $class the class to convert
	 * @param boolean $full return full path or not
	 * @return string the path
	 */
	static function toPath($class,$full = false){
		if($full){
			return self::path($class);
		}
		return str_replace('\\', DIRECTORY_SEPARATOR, $class);
	}
	
	/**
	 * Get a class relative to the project namespace
	 * 
	 * @param string $class Class to append to project space
	 * @param string $project Project space to use, null for default
	 * @return string
	 */
	static function getProjectSpace($class = '',$project = null){
		if($project === null){
			global $_PROJECT;
			$project = $_PROJECT;
		}
		return '\\'.$project.'\\'.ltrim($class,'\\');
	}
	
	/**
	 * Get classes by expression. Expressions use the glob format.
	 * 
	 * @param string $expr expression to search for
	 * @return array of classes
	 */
	static function get($expr){
		$ret = array();
	
		$path_expr = static::toPath($expr).'.php';
		//TODO: bootloader based ordering
		$paths = \AutoLoader::$pathCache;
		foreach($paths as $prefix){
			$prefixLen = strlen($prefix);
			foreach(glob($prefix.$path_expr) as $file){
				$key = substr(static::toClass($file),$prefixLen,-4);
				$ret[$key] = true;
			}
		}
	
		$ret = array_keys($ret);
		sort($ret);
		return $ret;
	}
	
	/**
	 * @see Libraries::get
	 */
	static function getNSExpression($expr){
		return self::get($expr);
	}
}