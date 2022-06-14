<?php
require('lib.php');

$fd = fopen('fixform.csv', 'r');
fgets($fd); fgets($fd); fgets($fd);
$sprotte = fgets($fd);
echo $sprotte . PHP_EOL;
