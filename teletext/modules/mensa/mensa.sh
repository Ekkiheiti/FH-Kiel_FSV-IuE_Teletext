#!/bin/bash
set -e

prepare() {
	echo -ne '\e[38;2;0;0;0m\e[48;2;255;255;0m'
	for i in {2..25}
	do
		echo -ne '\e['$i';0H\e[2K'
	done
	echo -ne '\e[2;0H'
}

cd $HOME/teletext/modules/mensa
./mensa-crawl.sh
(prepare; toilet 'Mensa heute'; php mensa.php today speiseplan.html) > ~/teletext/pages/300.tele 2>/dev/null
(prepare; toilet 'Mensa +1'; php mensa.php tomorrow speiseplan.html) > ~/teletext/pages/301.tele 2>/dev/null
(prepare; toilet 'Diner heute'; php mensa.php today speiseplan-diner.html) > ~/teletext/pages/302.tele 2>/dev/null
(prepare; toilet 'Diner +1'; php mensa.php tomorrow speiseplan-diner.html) > ~/teletext/pages/303.tele 2>/dev/null
killall -SIGUSR1 teletext 2>/dev/null
