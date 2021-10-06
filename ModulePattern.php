<?php

class JhpModule {
	protected $settings = [];
	protected $name;
	public static $list = [];

	private function PrepareName(string $path){
		return explode('.', basename($path))[0];
	}

	public function setName(string $name){
		$newName = $this->PrepareName($name);
		$this->name = $newName;
	}

	public function getName(){
		return $this->name;
	}

	public function setSettings(array $settings){
		$this->settings = $settings;
	}

	public function getSettings(){
		return $this->settings;
	}

	// public function action($function){
	// 	return $function();
	// }

	public static function addReg(string $regexp, string|callable $function){
		if (!array_key_exists($regexp, static::$list)){
			static::$list[$regexp] = $function;
		} else {
			throw new Exception('regexp is aelredy in $list');
		}
	}

	public function handle($code){
		foreach (static::$list as $regexp => $function){
			switch (gettype($function)) {
				case 'string':
					$code = preg_replace($regexp, $function, $code);
					break;
	
				case 'object': // function
					$code = preg_replace_callback($regexp, $function, $code);
					break;
				
				default:
					throw new Exception('$act is not NEED TYPE - (string|callable) "mphp" type is ' . gettype($act));
					break;
			}
		}
		return $code;
	}
}