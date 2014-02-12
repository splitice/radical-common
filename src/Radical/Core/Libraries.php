<?php
namespace Radical\Core;

use Composer\Autoload\ClassLoader;
class Libraries {
	/**
	 * @var \Radical\Cache\Object\ICache an optional cache for iterated queries
	 */
	public static $cache;
	
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
		$cache_key = md5(__DIR__).'_autoloader_bootstraps';
		$cached_files = apc_fetch($cache_key);
		if(!$cached_files || !Server::isProduction()){
			$cached_files = array();
			$all = self::composer_autoloader()->getPrefixes();
			foreach($all['Radical'] as $path){
				$bootstrap_path = $path.'/bootstrap.php';
				if(file_exists($bootstrap_path)){
					$cached_files[] = $bootstrap_path;
				}
			}
			
			if(Server::isProduction())
				apc_store($cache_key, $cached_files, 6000);
		}
		foreach($cached_files as $cf){
			include $cf;
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
	static function get($expr, $cache = true){
		if(self::$cache && $cache){
			$ret = self::$cache->get($expr);
			if(is_array($ret)){
				return $ret;
			}
		}
		
		$ret = array();
		
		$expr = ltrim($expr, '\\');
		$expr_path = str_replace('\\',DIRECTORY_SEPARATOR,$expr).'.php';
		$prefixes = self::composer_autoloader()->getPrefixes();
		$wildcard_pos = strpos($expr, '*');

		foreach ($prefixes as $prefix => $dirs) {
			$compare_len = strlen($prefix);
			if($compare_len > $wildcard_pos)
				$compare_len = $wildcard_pos;
			
			if ($wildcard_pos == 0 || 0 === substr_compare($expr, $prefix, 0, $compare_len)) {
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
		
		if(self::$cache && $cache){
			self::$cache->set($expr, $ret, 0);
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