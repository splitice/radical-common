<?php
namespace Radical\Core\ErrorHandling\Errors\Internal;

use Radical\Core\ErrorHandling\IErrorException;

abstract class ErrorException extends \Exception implements IErrorException {
	protected $heading;
	protected $fatal = false;
	
	function __construct($message,$heading = 'An error has occured',$fatal = false, \Exception $previous = null){
		$this->heading = $heading;
		$this->fatal = $fatal;
		parent::__construct($message,0,$previous);
	}

	/**
	 * @return the $header
	 */
	public function getHeading() {
		return $this->heading;
	}
	
	function isFatal(){
		return $this->fatal;
	}
	
	function getDebugMessage(){
		return $this->getMessage();
	}
	
	function getTraceOutput(){
		return $this->getTraceAsString();
	}
	
	function serialize(){
		$s = new SerializableErrorException($this);
		return $s->serialize();
	}
}