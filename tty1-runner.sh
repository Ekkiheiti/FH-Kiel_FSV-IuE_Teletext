#!/bin/bash
#./tty1-runner-classic.sh
cd /home/pi/teletext
cd cron
./daily
cd ..
killall teletext
killall reader.sh
./reader.sh &
(sleep 5; [ -f tele.load ] && killall -SIGUSR2 teletext) &
./teletext
