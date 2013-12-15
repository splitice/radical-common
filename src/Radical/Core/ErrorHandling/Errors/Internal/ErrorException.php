<?php
namespace Radical\Core\ErrorHandling\Errors\Internal;
use Radical\Web\Page\Controller;
use Radical\Core\ErrorHandling\IErrorException;

abstract class ErrorException extends \Exception implements IErrorException {
	protected $heading;
	protected $fatal = false;
	
	function __construct($message,$heading = 'An error has occured',$fatal = false){
		$this->heading = $heading;
		$this->fatal = $fatal;
		parent::__construct($message);
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

	function getPage(){
		return new Controller\Error($this);
	}
	
	function getTraceOutput(){
		return $this->getTraceAsString();
	}
	
	function serialize(){
		$s = new SerializableErrorException($this);
		return $s->serialize();
	}
}