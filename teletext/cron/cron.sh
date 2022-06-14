#!/bin/sh
cd /home/pi/teletext/cron/
cd $(basename $0).d/
find -type l -exec {} \;
