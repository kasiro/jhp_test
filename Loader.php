<?php

class Loader {
	public $modules = [];

	function __construct(){
		require 'ModulePattern.php';
	}

	public function addModule(string $PathToModule){
		$this->modules[] = $PathToModule;
	}

	public function process($code){
		foreach ($this->modules as $module){
			$moduleObject = require_once $module;
			$code = $moduleObject->handle($code);
		}
		return $code;
	}
}