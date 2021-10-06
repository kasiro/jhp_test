<?php

$module = new JhpModule;
$module->addreg('/(var|let) \{((?:(?(R).*|[^}]*+)|(?R))*)\} = (.*)/m', 'list($2) = $3');
$module->addreg('/(var|let) \[((?:(?(R).*|[^]]*+)|(?R))*)\] = (.*)/m', 'list($2) = $3');
return $module;