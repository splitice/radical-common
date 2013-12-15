<?php
namespace Radical\Core\Deployment\Remote;

interface IRemoteLocation {
	function writeFile($file,$data);
}