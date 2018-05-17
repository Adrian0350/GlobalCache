<?php

require_once 'GlobalCache/GlobalCache.php';


$IP2CC = new GlobalCache\IP2CC('192.168.1.223');

const RELAY_1 = '1:1';
const RELAY_2 = '1:2';
const RELAY_3 = '1:3';

$IP2CC->triggerRelay(RELAY_1, false);
$IP2CC->triggerRelay(RELAY_2, false);
$IP2CC->triggerRelay(RELAY_3, false);

echo ($IP2CC->disconnect() ? 'Disconnected' : 'Could not disconnect').PHP_EOL;
