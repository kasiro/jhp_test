<?php

require __DIR__.'/Loader.php';
require __DIR__.'/Logger.php';
require __DIR__.'/Config.php';

class App {
	public static $path = '';
	public static $CodeValid = true;
	public static $_instance = null;

	function __construct($path){
		static::$path = $path;
		if ($this->isValid($path)){
			$this->Logger = new Logger(__DIR__.'/log.txt');
			$this->Loader = Loader::getInstance();
			$this->Loader->Logger = &$this->Logger;
			$this->setGlobalPath($path);
		}
	}

	public function isValid($path){
		if (explode('.', basename($path))[1] !== 'jhp'){
			// Storage::get('Logger')->add('file is not jhp');
			// system('notify-send "JHP" "file is not jhp"');
			throw new Exception('file is not jhp');
		}
		return true;
	}

	public function setGlobalPath($path){
		$GLOBALS['fileinfo']['full'] = $path;
		$GLOBALS['fileinfo']['dirname'] = dirname($path);
		$GLOBALS['fileinfo']['basename'] = basename($path);
		$newpath = preg_replace('#\.[\w\d]+$#i', '.php', basename($path));
		$GLOBALS['fileinfo']['savefull'] = dirname($path).'/'.$newpath;
		foreach ($GLOBALS['fileinfo'] as $fname => $p){
			$this->Logger->add("file $fname path is '$p'");
		}
	}

	public function replacePath($path){
		$arr = explode('.', $path);
		$arr[count($arr) - 1] = 'php';
		return implode('.', $arr);
	}

	public function run(){
		if ($conf_path = Config::find(static::$path)){
			$GLOBALS['conf_path'] = $conf_path;
			$json = file_get_contents($GLOBALS['conf_path']);
			$this->loadAllModules([
				__DIR__.'/modules/*.php',
				__DIR__.'/larege_modules/*/index.php'
			]);
			if (strlen($json) == 0) {
				echo 'Заполняем конфиг' . PHP_EOL;
				if ($conf_path != './jhp.config') $this->Logger->add("Заполняем конфиг '$conf_path'");
				Config::create($GLOBALS['conf_path'], 'use');
				$json = file_get_contents($GLOBALS['conf_path']);
				$this->elseHandler($GLOBALS['conf_path']);
			} else {
				$this->elseHandler($GLOBALS['conf_path']);
			}
		} else {
			$this->Logger->add('Конфиг не найден');
			echo 'Конфиг не найден' . PHP_EOL;
		}
		$code = file_get_contents(static::$path);
		$beforeCode = $code;
		// print_r($this->Loader->getModules());
		$code = $this->Loader->process($code);
		file_put_contents(
			$this->replacePath(static::$path),
			$code
		);
		if ($beforeCode == $code){
			$this->Logger->add('Код не изменился');
			App::$CodeValid = false;
		}
	}

	public function elseHandler($conf_path){
		if ($conf_path != './jhp.config') $this->Logger->add("Обрабатываем данные конфига '$conf_path'");
		echo 'Обрабатываем данные конфига' . PHP_EOL;
		$this->Loader->preloadModules($conf_path);
	}

	public static function getInstance(...$args) {
		if (self::$_instance === null) {
			self::$_instance = new self(...$args);
		}
		return self::$_instance;
	}
 
	private function __clone() {}
	public function __wakeup() {}

	public function loadModules($path){
		$modules = glob($path);

		foreach ($modules as $modulePath) {
			$isLoaded = $this->Loader->addModule($modulePath);
			$module_type = basename($modulePath) == 'index.php' ? 'large' : 'module';
			if ($module_type == 'large'){
				$mname = basename(dirname($modulePath));
			} else {
				$mname = basename($modulePath);
			}
			echo ($isLoaded ? 'Модуль ('.$mname.') загружен' : 'Модуль ('.$mname.') не валиден') . PHP_EOL;
		}
	}

	public function loadAllModules($modules){
		foreach ($modules as $modulesPath){
			$this->loadModules($modulesPath);
		}
	}
}
$App = new App($argv[1]);
$App->run();
$App->Logger->ot();
if (!App::$CodeValid){
	echo 'Код не изменился', PHP_EOL;
}