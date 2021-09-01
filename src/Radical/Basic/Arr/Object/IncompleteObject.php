<?php
namespace Radical\Basic\Arr\Object;

/**
 * An array object that doesnt know what it contains until it is initialized.
 * 
 * @author SplitIce
 *
 */
abstract class IncompleteObject implements \IteratorAggregate, \ArrayAccess, \Serializable, \Countable {
	protected $data = null;
	private $cache = false;
	abstract function getData();
	abstract function yieldData();

	function cache($enable = true){
		$this->cache = $enable;
	}

	function init(){
		if($this->data === null){
			$this->data = $this->getData();
		}
	}
	
	/* IteratorAggregate */
	function getIterator() {
		if($this->cache()) {
			$this->Init();
			return new \ArrayIterator($this->data);
		}else{
			return $this->yieldData();
		}
	}
	
	/* ArrayAccess */
	public function offsetSet($offset, $v) {
		if (is_null($offset)) {
			$this->_Set($this->Count(),$v);
		} else {
			$this->_Set($offset,$v);
		}
	}
	public function offsetExists($offset) {
		$this->Init();
		return isset($this->data[$offset]);
	}
	public function offsetUnset($offset) {
		$this->Init();
		unset($this->data[$offset]);
	}
	public function offsetGet($offset) {
		return $this->_Get($offset);
	}
	
	/* Serializable */
	public function serialize() {
		$this->Init();
		return serialize($this->data);
	}
	public function unserialize($data) {
		$this->data = unserialize($data);
	}
	
	/* Countable */
	public function count(){
		$this->Init();
		return count($this->data);
	}
	
	/* IncompleteObject */
	
	function _Set($k,$v){
		$this->Init();
		$this->data[$k] = $v;
	}
	function _Add($k,$v){
		$this->Init();
		$ret = isset($this->data[$k]);
		$this->_Set($k,$v);
		return $ret;
	}
	function _Get($k){
		$this->Init();
		if(isset($this->data[$k])){
			return $this->data[$k];
		}
	}
	function set($k,$v){
		$this->_Set($k,$v);
	}
	function Add($k,$v){
		return $this->_Add($k,$v);
	}
	function get($k){
		return $this->_Get($k);
	}
	function remove($k){
		$this->Init();
		unset($this->data[$k]);
	}
	function hasData(){
	    return $this->data !== null;
    }

	/**
	 * @return array
	 */
	function toArray(){
		$this->Init();
		return $this->data;
	}
	function isAssoc () {
		$this->Init();
		$arr = $this->data;
        return (is_array($arr) && (!count($arr) || count(array_filter(array_keys($arr),'is_string')) == count($arr)));
    }
    function getAll(){
    	$this->Init();
    	return $this->data;
    }
}