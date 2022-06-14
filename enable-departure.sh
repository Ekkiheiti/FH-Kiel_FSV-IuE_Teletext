#!/bin/sh
if [ "x$(cat /tmp/tty1state)" != "xdeparture" ]
then
	killall monitor
	killall aafire
	killall fbi
	killall cvlc
fi
