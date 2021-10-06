<?php

class Loader {
	public $modules = [];

	function __construct(){
		require __DIR__.'/ModulePattern.php';
	}

	public function addModule(string $PathToModule){
		$this->modules[$PathToModule] = $module = require_once $PathToModule;
		$module_type = basename($PathToModule) == 'index.php' ? 'large' : 'module';
		if ($module_type == 'large'){
			$name = $module->getName();
			$this->Logger->add("load large module: '$name'");
		} else {
			$name = $module->getName();
			$this->Logger->add("load module: '$name'");
		}
	}

	public function process($code){
		foreach ($this->modules as $pathToModule => $module){
			if (is_object($module)){
				$code = $module->handle($code);
			}
		}
		return $code;
	}
}