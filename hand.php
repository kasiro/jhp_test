<?php

require __DIR__.'/Loader.php';

class App {
	public static $path = '';

	function __construct($path){
		static::$path = $path;
		if ($this->isValid(static::$path)){
			$this->Loader = new Loader();
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

	public function replacePath($path){
		$arr = explode('.', $path);
		$arr[count($arr) - 1] = 'php';
		return implode('.', $arr);
	}

	public function run(){
		$code = file_get_contents(static::$path);
		$this->LoadModules();
		$code = $this->Loader->process($code);
		file_put_contents($this->replacePath(static::$path), $code);
	}

	public function LoadModules(){
		$modules = glob(__DIR__.'/modules/*.php');

		foreach ($modules as $module){
			$this->Loader->addModule($module);
		}
	}
}
$App = new App($argv[1]);
$App->run();