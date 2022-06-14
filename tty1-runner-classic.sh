#!/bin/bash
set -e

if [ -f /tmp/aafire ]
then
	rm /tmp/aafire
	echo aafire > /tmp/tty1state
	cd /home/pi
	cvlc campfire.aiff &
	aafire
	exit
fi

if [ -f /tmp/fslogo ]
then
	rm /tmp/fslogo
	echo fslogo > /tmp/tty1state
	cd /home/pi
	fbi -a logo-monitor/*.png -t 20
	exit
fi

echo departure > /tmp/tty1state
cd /home/pi/departure-monitor
echo "Crawling Schwentinemensa data..."
./mensa-crawl.sh
php mensa.php > mensa.txt 2>/dev/null
./run.sh &
./monitor $(echo $(cat mensa.txt | wc -l)-1|bc)
