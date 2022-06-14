#!/bin/sh
if [ "x$(cat /tmp/tty1state)" != "xaafire" ]
then
	touch /tmp/aafire
	killall aafire
	killall fbi
	killall cvlc
	killall monitor
fi
