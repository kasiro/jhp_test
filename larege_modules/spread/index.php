<?php declare(strict_types=1);

require __DIR__.'/func.php';
// Test Module
$module = new JhpModule;
$module->setSettings([
	'use' => false
]);
$module->setName(basename(dirname(__FILE__)), false);
$module->addreg('/\[(.*?)\];/ms', function ($matches){
	return mystart($matches, [
		false,
		false
	]);
});
return $module;