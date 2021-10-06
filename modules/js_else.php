<?php

$module = new JhpModule;
$module->setName(__FILE__);
$module->addreg('/(var|let) \{((?:(?(R).*|[^}]*+)|(?R))*)\} = (.*)/m', 'list($2) = $3');
$module->addreg('/(var|let) \[((?:(?(R).*|[^]]*+)|(?R))*)\] = (.*)/m', 'list($2) = $3');
return $module;