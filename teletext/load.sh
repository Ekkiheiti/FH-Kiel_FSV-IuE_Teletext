#!/bin/sh
cd /home/pi/teletext
echo $1 > tele.load
killall -SIGUSR2 teletext
