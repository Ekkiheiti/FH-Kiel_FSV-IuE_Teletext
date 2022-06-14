#!/bin/bash
set -e

prepare() {
	echo -ne '\e[38;2;255;255;255m\e[48;2;0;255;0m'
	for i in {2..25}
	do
		echo -ne '\e['$i';0H\e[2K'
	done
	echo -ne '\e[2;0H'
}

cd ~/teletext/modules/departure
php monitor.php > monitor.json
php fixform.php > fixform.csv
php hafas.php 2>/dev/null | head -n24 | head -c-1 > ~/teletext/pages/101.tele
php fixts.php TRAIN 2>/dev/null | head -n24 | head -c-1 > ~/teletext/pages/102.tele
(prepare; echo; echo; echo; echo; echo; echo; ((echo -n '     '; php sprotte.php) | toilet); echo; echo '               verfügbare Fahrräder an der Sprottenflotten-Station') > ~/teletext/pages/200.tele
killall -SIGUSR1 teletext 2>/dev/null
