<?php
namespace Radical\Core;

/**
 * Get infomation about the server / script deployment
 * 
 * @author SplitIce
 *
 */
class Server {
	static $production = true;
	/**
	 * Is this site running in a production environment?
	 * 
	 * @return boolean
	 */
	static function isProduction(){
		if(!isset($_SERVER['SERVER_ADDR'])) return self::$production;
		if($_SERVER['SERVER_ADDR'] === '::1' || $_SERVER['SERVER_ADDR'] === '127.0.0.1')
			return false;

		return self::$production;
	}
	static function getEnvironment(){
	    $env = $_ENV['x4b_environment'];
	    return $env;
    }
	
	/**
	 * Is this site running in Command Line mode?
	 * 
	 * @return boolean
	 */
	static function isCLI(){
		return (PHP_SAPI === 'cli');
	}
	
	/**
	 * Is this script running on windows?
	 * 
	 * @return boolean
	 */
	static function isWindows(){
		return (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
	}
	
	static function scheme(){
		if(empty($_SERVER['HTTPS'])){
			return 'http://';
		}
		return 'https://';
	}
	
	/**
	 * Get the directory root for the site.
	 * 
	 * @return string
	 */
	static function getSiteRoot(){
		global $WEBPATH;
		if(!isset($WEBPATH))
			return '/';
		return $WEBPATH.'/';
	}
}