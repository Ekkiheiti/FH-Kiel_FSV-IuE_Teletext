#!/bin/sh
if [ "x$(cat /tmp/tty1state)" != "xfslogo" ]
then
	touch /tmp/fslogo
	killall aafire
	killall cvlc
	killall fbi
	killall monitor
fi
