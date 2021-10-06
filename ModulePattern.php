<?php

class JhpModule {
	public static $list = [];
	public static $listArgs = [];

	public static function addReg(string $regexp, string|callable $function){
		static::$list[$regexp] = $function;
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
					throw new Exception('$act is not NEED TYPE (mphp) type is ' . gettype($act));
					break;
			}
		}
		return $code;
	}
}