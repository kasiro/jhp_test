<?php

if (!class_exists('JhpException')) require_once __DIR__.'/jhpException.php';
if (!class_exists('jModule')){
	class jModule {
		protected $settings = [];
		protected $name;
		public static $list = [];
	
		private function PrepareName(string $path){
			return explode('.', basename($path))[0];
		}
	
		public function setName(string $name, $prepare = true){
			if ($prepare){
				$newName = $this->PrepareName($name);
			} else {
				$newName = $name;
			}
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
	
		public function addreg(string $regexp, string|callable $function){
			if (!array_key_exists($regexp, static::$list)){
				static::$list[$regexp] = $function;
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
						$errMessage = '$function is not NEED TYPE - (string|callable) "JHP (hand)" type is ' . gettype($function);
						$Logger = new Logger(__DIR__.'/log.txt');
						$Logger->add($errMessage);
						throw new JhpException($errMessage);
						break;
				}
			}
			return $code;
		}
	}
}