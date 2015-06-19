<?php
namespace Radical\CLI\Output\Internal;
use Radical\CLI\Output;
use Radical\CLI\Output\OutputHandler;

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