<?php

require __DIR__.'/import/func.php';

if (!class_exists('JhpHelper')){
	class JhpHelper {
		public static function getImportModules(array|string $path){
			$modules = [];
			$allvars = get_defined_vars();
			$getMod = function($path) use ($allvars) {
				extract($allvars);
				$text = file_get_contents($path);
				preg_replace_callback('/(.*)import: include \'(.*)\';/m', function ($matches) use (&$modules) {
					$modules[] = $matches[2];
				}, $text);
				preg_replace_callback('/(\t*|\s*)import_array: include \[((?:(?(R)\w++|[^]]*+)|(?R))*)\];/m', function ($matches) use (&$modules) {
					foreach (import_array($matches[2]) as $module){
						$modules[] = $module;
					}
				}, $text);
				return $modules;
			};
			if (is_string($path)){
				return $getMod($path);
			} elseif (is_array($path)){
				foreach ($path as $file_path){
					$modules = array_merge($modules, $getMod($path));
				}
				return $modules;
			}
			return false;
		}
	}
}