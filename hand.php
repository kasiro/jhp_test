<?php

require __DIR__.'/Loader.php';
require __DIR__.'/Logger.php';

class App {
	public static $path = '';

	function __construct($path){
		static::$path = $path;
		if ($this->isValid($path)){
			$this->Logger = new Logger(__DIR__.'/log.txt');
			$this->Loader = new Loader();
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
		$code = file_get_contents(static::$path);
		$beforeCode = $code;
		$this->loadAllModules();
		$code = $this->Loader->process($code);
		file_put_contents($this->replacePath(static::$path), $code);
		if ($beforeCode == $code){
			$this->Logger->add('Код не изменился');
		}
	}

	public function loadModules($path){
		$modules = glob($path);

		foreach ($modules as $module){
			$this->Loader->addModule($module);
		}
	}

	public function loadAllModules(){
		$this->loadModules(__DIR__.'/modules/*.php');
		$this->loadModules(__DIR__.'/larege_modules/*/index.php');
	}
}
$App = new App($argv[1]);
$App->run();
$App->Logger->ot();