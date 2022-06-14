<?php
require('lib.php');

$coolcol = "\e[38;2;255;0;0m";
$medcol = "\e[38;2;255;255;0m";
$hotcol = "\e[38;2;0;255;0m";
$colres = "\e[38;2;255;255;255m";
echo $colres;
$fd = fopen('fixform.csv', 'r');
fgets($fd); fgets($fd); fgets($fd); fgets($fd);
while($line = fgetcsv($fd, 0, ';')) {
	$col = '';
	if($line[5] >= 0 && $line[5] < 2) $col = $hotcol;
	else if($line[5] >= 0 && $line[5] < 5) $col = $medcol;
	else if($line[5] >= 5) $col = $coolcol;
	mb_printf("\n\e[0m%7s%-60s   %s%s%s\n", '', substr($line[3], 0, 57), $col . "\e[1m", $line[4], $colres . "\e[0m");
	mb_printf("\e[1m%5s  %-60s   ", $line[1], $line[2]);
	if($line[5] >= 0) {
		printf("%s(%+d)  %s", $col, $line[5], $colres);
	} else printf("%5s", '');
	echo "\n";
}
