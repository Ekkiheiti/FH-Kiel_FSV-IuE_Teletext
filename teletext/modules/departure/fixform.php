<?php
$relstations = [
	'9911218', // Seefischmarkt
	//'706800',  // Wellingdorf
	//'9910447', // HDW
	//'9910446', // HDW
	//'4310251', // KVG-Betriebshof Werftstr.
	'3440044', // Hbf
	'706821',  // Hbf
	'3440015', // Dreiecksplatz
	'3440032', // Ansgarkirche
	'706974',  // Uni
	//'400821',  // Hardenbergstr.
	'3440020', // Elendsredder

	'9910404', // Fachhochschule
	//'9911195', // Salzredder
	'9910457', // Hermannstr.
	'706836',  // Mönkeberg Gänsekrug
	'720006',  // Heikendorf Rathaus
	'9905392', // Brodersdorf
	'720607',  // Laboe Kirche

	'4300950', // Wellingdorfer Brücke
	'4300903', // Reventloubrücke
];

function parseSprotte($data) {
	echo $data['num_bikes_available'] . PHP_EOL;
}

function fixStationName($title) {
	if(substr($title, 0, 4) == 'Kiel') $title = substr($title, 5);
	if(substr($title, -7) == 'straße') $title = substr($title, 0, -4) . '.';
	if($title == 'Hauptbahnhof') $title = 'Hbf';
	if(substr($title, -9) == ' (Fähre)') $title = substr($title, 0, -9);
	return $title;
}

function parseHafas($data, $alt) {
	global $relstations;
	foreach($data as $linedata) {
		if(isset($linedata['departure']['delay']) && $linedata['departure']['delay'] > 300) continue;
		$ferry = $linedata['train']['line'][0] == 'F';
		$line = $linedata['train']['line'];
		$stops = [];
		foreach($linedata['stops'] as $i => $stop) {
			if($i == 0 || $i == count($linedata['stops']) - 1) continue;
			$title = fixStationName($stop['station']['title']);
			if(in_array($stop['station']['id'], $relstations)) {
				$arrival = strftime('%H:%M', strtotime($stop['arrival']['time']));
				$stops[] = $title . ' ' . $arrival;
			}
			// add new stations via:
			//else $stops[] = $title . '(' . $stop['station']['id'] . ')';
		}
		$dest = $linedata['finalDestination'];
		if(substr($dest, 0, 4) == 'Kiel') $dest = substr($dest, 5);
		if($dest == 'Schwentinestraße') continue;
		$depart = strftime('%H:%M', strtotime($linedata['departure']['time']));
		$arrive = strtotime($linedata['stops'][count($linedata['stops']) - 1]['arrival']['time']);
		$dest .= ' ' . strftime('%H:%M', $arrive);
		if(isset($linedata['departure']['delay'])) {
			$delay = $linedata['departure']['delay'];
		} else {
			$delay = -1;
		}
		if($delay > 60) continue;
		$ferrystops = [];
		if($ferry) {
			foreach($alt as $altlinedata) {
				$altdepart = strtotime($altlinedata['departure']['time']);
				if($altdepart < $arrive) continue;
				if($altdepart > $arrive + 1800) continue;
				if($linedata['train']['line'] == $altlinedata['train']['line']) continue;
				if(substr($altlinedata['train']['line'], 0, 1) != 'F') continue;
				$ferrystops[] = $altlinedata['train']['line'] . ' ' . fixStationName($altlinedata['finalDestination']) . ' ab ' . strftime('%H:%M', $altdepart);
				break;
			}
		}
		$outs = [
			$ferry ? 'F' : 'S',
			$line ?: '-',
			($dest ?: 'Betriebsfahrt') . (count($ferrystops) ? ' -> ' . implode(', ', $ferrystops) : ''),
			implode(' • ', $stops) ?: ' ',
			$depart ?: '--:--',
			$delay,
		];
		echo implode(';', $outs) . PHP_EOL;
	}
}

$data = json_decode(file_get_contents('monitor.json'), true);
echo exec('ip -4 a show wlan0 | grep \'inet \' | cut -c10- | cut -d\/ -f1') . PHP_EOL;
echo strftime('%H:%M') . PHP_EOL;
echo ((strftime('%H') * 60 + strftime('%M') * 1) > 14*60+15 ? 0 : 1) . PHP_EOL;
parseSprotte($data['sprotte']);
parseHafas($data['hafas'], $data['hafasAlt']);
