<?php
namespace Radical\Core;

use Composer\Autoload\ClassLoader;
class Libraries {
	/**
	 * @return \Composer\Autoload\ClassLoader
	 */
	static function composer_autoloader(){
		static $once = false;
		if($once === false){
			foreach(spl_autoload_functions () as $a){
				if(is_array($a)){
					$a = $a[0];
					if($a instanceof ClassLoader){
						$once = $a;
						break;
					}
				}
			}
		}
		return $once;
	}
	
	static function onload(){
		$all = self::composer_autoloader()->getPrefixes();
		foreach($all['Radical'] as $path){
			$bootstrap_path = $path.'/bootstrap.php';
			if(file_exists($bootstrap_path)){
				include $bootstrap_path;
			}
		}
	}
	
	/**
	 * Resolves a relative path to class the
	 * appropriate full class path.
	 * 
	 * @param string $path partial path to class
	 * @return string
	 */
	static function path($path){
		return self::composer_autoloader()->findFile(self::toClass($path));
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
	
	static function getAllClass(){
		return array_keys(self::composer_autoloader()->getClassMap());
	}
	
	/**
	 * Get classes by expression. Expressions use the glob format.
	 * 
	 * @param string $expr expression to search for
	 * @return array of classes
	 */
	static function get($expr){
		$ret = array();
		
		$expr = ltrim($expr, '\\');
		$expr_path = str_replace('\\',DIRECTORY_SEPARATOR,$expr).'.php';
		$prefixes = self::composer_autoloader()->getPrefixes();

		foreach ($prefixes as $prefix => $dirs) {
			if (0 === strpos($expr, $prefix)) {
				foreach ($dirs as $dir) {
					$s = strlen($dir);
					$g = glob($dir . DIRECTORY_SEPARATOR. $expr_path);
					foreach($g as $v){
						$c = substr($v,$s);
						$c = substr($c,0,-4);
						$ret[] = str_replace(DIRECTORY_SEPARATOR, '\\', $c);
					}
				}
			}
		}
		
		return $ret;
	}
	
	/**
	 * @see Libraries::get
	 */
	static function getNSExpression($expr){
		return self::get($expr);
	}
}