<?php
namespace Radical\CLI\Output\Internal;
use Radical\CLI\Output\OutputHandler;
use Radical\CLI\Output;

abstract class OutputBase extends OutputHandler {
	static function e(){
		$s = '';
		foreach(func_get_args() as $a){
			$s .= $a;
		}
		Output\Log::Get()->Write($s);
		return $s;
	}
}