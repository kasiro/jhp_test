<?php

class Config {
	public static $config_name = '';

	public static function find($file_path){
		$arr = explode('/', dirname($file_path));
		$DirPath = dirname($file_path);
		for ($i = 0; $i < count($arr); $i++){
			$cur_dir_up = $DirPath;
			$files = glob($cur_dir_up . '/*.config');
			if (!empty($files)){
				foreach ($files as $file){
					if (basename($file) == 'jhp.config'){
						return $file;
					}
				}
			}
			$DirPath = dirname($DirPath);
		}
		return false;
	}

	public static function create($conf_path, $mode = 'all'){
		$Logger = new Logger(__DIR__.'/log.txt');
		$Loader = Loader::getInstance();
		$config = [
			'modules' => [],
			'aliases' => [
				'__con' => '__construct'
			]
		];
		if (!empty($Loader->getModules())){
			foreach ($Loader->getModules() as $path => $current_module){
				$current_module = require $path;
				if (is_object($current_module)){
					$module_name = $current_module->getName();
					$module_settings = $current_module->getSettings();
					if ($mode == 'all') {
						$config['modules'][] = [
							$module_name => $module_settings
						];
					} elseif ($mode == 'use'){
						if ($module_settings['use']) {
							$config['modules'][] = [
								$module_name => $module_settings
							];
						}
					}
				}
			}
			$Logger->add("create_start_config mode is '$mode'");
			$j = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			$j = str_replace('    ', "\t", $j);
			file_put_contents($conf_path, $j);
		} else {
			throw new JhpException('module list is empty');
		}
	}
}