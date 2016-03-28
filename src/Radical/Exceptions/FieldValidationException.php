<?php
namespace Radical\Exceptions;

class FieldValidationException extends ValidationException {
	protected $field;
	function __construct($field = null){
		$this->field = $field;
		$message = 'A validation Exception occurred';
		if($field){
			$message .= ' with '.$field;
		}
		parent::__construct($message);
	}
	
	/**
	 * @return string $field
	 */
	public function getField() {
		return $this->field;
	}
}