<?php
$idSprotte = '17627512';
$idHafas = '009049055';
$idHafasAlt = '009049207';
$idHafasTS = '009049079';

function get($url, $fn) {
        $ffn = 'cache/' . $fn . '.json';
        if(file_exists($ffn)) {
		$content = file_get_contents($ffn);
                $mtime = 30;
                if(filemtime($ffn) < (time() - $mtime)) {
                        $content = null;
                }
        }
        if(!isset($content) || !$content) {
                $content = file_get_contents($url);
                file_put_contents($ffn, $content, LOCK_EX);
        }
        return json_decode($content, true);
}

$bigSprotte = get('https://gbfs.nextbike.net/maps/gbfs/v1/nextbike_sf/de/station_status.json', 'sprotte');
$sprotte = [];
foreach($bigSprotte['data']['stations'] as $station) {
	if($station['station_id'] == $idSprotte) {
		$sprotte = $station;
		break;
	}
}

$hafas = get('https://marudor.de/api/hafas/v2/departureStationBoard?station=' . $idHafas . '&profile=nahsh', 'hafas');
$hafasAlt = get('https://marudor.de/api/hafas/v2/departureStationBoard?station=' . $idHafasAlt . '&profile=nahsh', 'hafasalt');
$hafasTS = get('https://marudor.de/api/hafas/v2/departureStationBoard?station=' . $idHafasTS . '&profile=nahsh', 'hafasts');

header('Content-Type: text/json');
echo json_encode([
	'sprotte' => $sprotte,
	'hafas' => $hafas,
	'hafasAlt' => $hafasAlt,
	'hafasTS' => $hafasTS,
]);
