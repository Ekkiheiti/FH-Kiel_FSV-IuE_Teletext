<?php
require('lib.php');
$coolcol = "\e[38;2;255;0;0m";
$medcol = "\e[38;2;255;255;0m";
$hotcol = "\e[38;2;0;255;0m";
$colres = "\e[38;2;255;255;255m";
echo $colres;

$data = json_decode(file_get_contents('monitor.json'), true);
$hafas = $data['hafasTS'];
$alt = $data['hafas'];

$sts = [
	'8003474', // Citti-Park
	'8003479', // Kronshagen
	'8005781', // Suchsdorf
	'8002266', // Gettorf
	'8001654', // Eckernförde
	//'8001136', // Bredenbek
	//'8005435', // Schülldorf

	//'8004741', // Owschlag
	'8005362', // Schleswig
	//'8000334', // Jübek

	//'8003253', // Russee
	'8003960', // Melsdorf
	'8001977', // Felde
	//'8000416', // Achterwehr
	'8000312', // Rendsburg

	//'8002011', // Flintbek
	//'8001092', // Bordesholm
	//'8001718', // Einfeld
	'8000271', // Neumünster
	//'8001190', // Brokstedt
	//'8006572', // Wrist
	'8000092', // Elmshorn
	'8002548', // Dammtor
	'8002549', // Hamburg

	'8000168', // Uelzen (DB)
	'8010222', // Wittenberg (DB)
	'8010205', // Leipzig (DB)
	'8010101', // Erfurt (DB)
	'8001844', // Erlangen (DB)
	'8000284', // Nürnberg (DB)

	//'8010310', // Salzwedel (DB)
	//'8010334', // Stendal (DB)
	//'8010404', // Berlin-Spandau (DB)
	'8098160', // Berlin Hbf Fern (DB)
	//'8011113', // Berlin-Südkreuz (DB)

	//'8003477', // Elmschenhagen
	'8004924', // Raisdorf
	'8004879', // Preetz
	//'8000011', // Ascheberg(Holst)
	'8004841', // Plön
	//'8003829', // Bad Malente-Gremsmühlen
	//'8001941', // Eutin
	'8000749', // Bad Schwartau
	//'8004848', // Pönitz(Holst)
	//'8004750', // Pansdorf

	'8003251', // Schulen am Langsee
	'8003259', // Ellerbek
];

foreach($hafas as $line) {
	if(trim($line['train']['type']) == 'Bus') continue;
	$dts = strtotime($line['departure']['time']);
	if(($dts - time()) < 900) continue;
	$stops = [];
	array_shift($line['stops']);
	$ads = array_pop($line['stops']);
	foreach($line['stops'] as $stop) {
		if(!in_array($stop['station']['id'], $sts)) continue;
		$title = $stop['station']['title'];
		if($title == 'Kiel-Hassee CITTI-PARK') $title = 'Hassee';
		if(substr($title, 0, 4) == 'Kiel') $title = substr($title, 5);
		if($title == 'Schwentinental-Raisdorf') $title = 'Raisdorf';
		if(substr($title, -6) == '(tief)') $title = trim(substr($title, 0, -6));
		if(substr($title, -7) == '(Holst)') $title = trim(substr($title, 0, -7));
		if(substr($title, -6) == '(Bahn)') $title = trim(substr($title, 0, -6));
		if(substr($title, -7) == 'Bahnhof') $title = trim(substr($title, 0, -7));
		if(substr($title, -3) == 'Hbf') $title = trim(substr($title, 0, -3));
		if($title == 'Hamburg Dammtor (Messe/CCH)') $title = 'HH-Dammtor';
		if(substr($title, 0, 7) == 'Hamburg' && strlen($title) > 7) $title = 'HH-' . trim(substr($title, 7));
		$ats = strftime('%H:%M', strtotime($stop['arrival']['time']));
		$stops[] = $title . ' ' . $ats;
		//$stops[] = $stop['station']['title'] . ' (' . $stop['station']['id'] . ')';
	}
	$col = '';
	if($line['departure']['delay'] >= 0 && $line['departure']['delay'] < 2) $col = $hotcol;
	else if($line['departure']['delay'] >= 0 && $line['departure']['delay'] < 5) $col = $medcol;
	else if($line['departure']['delay'] >= 5) $col = $coolcol;
	$dt = strftime('%H:%M', strtotime($line['departure']['time']));
	mb_printf("\n\e[0m%8s  %-57s  %s%s ", '', mb_substr(implode(' • ', $stops), 0, 57), $col . "\e[1m", $dt);
	if($line['departure']['delay'] >= 0) {
		printf("(%+d)", $line['departure']['delay']);
	} else printf("%5s", '');
	echo $colres . "\e[0m" . PHP_EOL;
	$ats = strftime('%H:%M', strtotime($ads['arrival']['time']));
	$tl = $line['train']['name'];
	$td = $line['finalDestination'] . ' ' . $ats;
	foreach(array_reverse($alt) as $subline) {
		foreach($subline['stops'] as $substop) {
			if($substop['station']['id'] != '3440044' && $substop['station']['id'] != '706821') continue;
			if($dts - strtotime($substop['arrival']['time']) < 360) continue;
			break 2;
		}
		$substop = null;
	}
	if($substop) $td .= ' (' . $subline['train']['line'] . ' ab ' . strftime('%H:%M', strtotime($subline['departure']['time'])) . ')';
	if($line['train']['operator']['id'] == '1805') $tl = 'FLX ' . $tl;
	mb_printf("\e[1m%8s  %-57s  Hbf Gl. %-2s\n", $tl, $td, $line['departure']['platform']);
}
