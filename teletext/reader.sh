#!/bin/sh
cec-client | while read line
do
	parsedline="$(echo $line | egrep "key pressed: .* 0)")"
	if [ "x$parsedline" = "x" ]; then continue; fi
	keyline="$(echo $line | cut -d\: -f3 | cut -d\( -f1)"
	echo $keyline
done >> tele.pipe
