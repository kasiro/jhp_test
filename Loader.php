<?php

class Loader {
	public $modules = [];
	public $load_modules = [];
	public static $_instance = null;

	function __construct(){
		require __DIR__.'/ModulePattern.php';
	}

	public static function getInstance(...$args) {
		if (self::$_instance === null) {
			self::$_instance = new self(...$args);
		}
		return self::$_instance;
	}
 
	private function __clone() {}
	public function __wakeup() {}

	/**
	 * update_modules_config Modules Settings
	 *
	 * @return void
	 */
	public function preloadModules($conf_path){
		$config = file_get_contents($conf_path);
		$config = json_decode($config, true);
		$modules = $config['modules'];
		$newModules = [];
		foreach ($modules as $el){
			foreach ($el as $key => $val){
				$newModules[$key] = $val;
			} 
		}
		// print_r($newModules);
		unset($modules);
		if ($this->load_modules != $newModules){
			if (!empty($newModules)){
				$this->load_modules = $newModules;
			}
		}
		$tempArray = [];
		if (!empty($this->modules)){
			foreach ($this->modules as $path => $object){
				$newobject = require $path;
				if (in_array($newobject->getName(), array_keys($this->load_modules))){
					$newobject->setSettings($this->load_modules[$newobject->getName()]);
					if ($newobject->getSettings()['use'] === true){
						$tempArray[$path] = $newobject;
					}
					// echo 'preloadModule: ', basename($path), PHP_EOL;
				} else {
					echo 'load_modules not isset: ', $newobject->getName(), PHP_EOL;
				}	
			}
			$this->modules = $tempArray;
		}
	}

	public static function findPathModule($filePath){}

	public function addModule(string $PathToModule){
		$module = require $PathToModule;
		if (is_object($module)){
			if ($module->getSettings()['use'] === true){
				$this->modules[$PathToModule] = $module;
				$module_type = basename($PathToModule) == 'index.php' ? 'large' : 'module';
				if ($module_type == 'large'){
					$name = $module->getName();
					$this->Logger->add("load large module: '$name'");
				} else {
					$name = $module->getName();
					$this->Logger->add("load module: '$name'");
				}
				return true;
			}
		}
		return false;
	}

	public function getModules(){
		return $this->modules;
	}

	public function getModulesObjects(){
		foreach ($this->modules as $path => $object){
			yield $object;
		}
	}

	public function process($code){
		// FIXME: ОБРАБАТЫВАЮТСЯ ВСЕ МОДУЛИ ДАЖЕ ВЫКЛЮЧЕННЫЕ И УБРАННЫЕ ИЗ СПИСКА
		foreach ($this->modules as $pathToModule => $module){
			if (is_object($module) && $module->getSettings()['use'] === true){
				$code = $module->handle($code);
			}
		}
		return $code;
	}
}