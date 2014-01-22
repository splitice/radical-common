<?php
namespace Radical\Basic\Arr;

/**
 * Group a set based on the result of a callback function.
 * 
 * @author SplitIce
 */
class Group {
	private $set = array();
	
	function __construct($set){
		$this->set = $set;
	}
	
	function group($function){
		if(!is_callable($function)){
			throw new \Exception('Callback isnt callable, cant group');
		}
		
		//Group
		$ret = array();
		foreach($this->set as $k=>$v){
			$group = $function($k,$v);
			
			if(!isset($ret[$group])){
				$ret[$group] = array();
			}
			
			$ret[$group][$k] = $v;
		}
		
		return $ret;
	}
}