<?php

require '/home/kasiro/Документы/projects/rahman_jhp/user_modules/fs.php';

$arr = ['key', 'val'];
list($key, $val) = $arr;

$console_log = function ($command) {
	echo '$ ' . $command . PHP_EOL;
};
$var = 'fn() => main';
$new_arr['getState'] = function ($state, $func) use ($console_log) {
	$console_log("state is '$state'");
	$console_log('func is \'' . $func('fall') . ''');
};
$new_arr['getState']('zero', function ($el) {
	return $el;
});

$main = @$new_arr ? $new_arr : (
	@$key ? $key : null
);