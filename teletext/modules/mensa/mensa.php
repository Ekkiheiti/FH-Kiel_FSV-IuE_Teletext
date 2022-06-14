<?php
$doc = new DOMDocument();
$doc->loadHTMLFile($argv[2]);
$main = $doc->getElementById('days');

foreach($main->childNodes as $child) {
	if($child->nodeName != 'div') continue;
	$daylabel = $child->attributes->getNamedItem('longdesc')->nodeValue;
	if($daylabel != strftime('%Y-%m-%d', strtotime($argv[1]))) continue;
	//echo $daylabel . PHP_EOL;
	foreach($child->childNodes as $day) {
		if($day->nodeName != 'table') continue;
		foreach($day->childNodes as $tbl) {
			$price = '';
			if($tbl->nodeName != 'tr') continue;
			$label = [];
			foreach($tbl->childNodes as $tc) {
				if($tc->nodeName != 'td') continue;
				foreach($tc->childNodes as $td) {
					if($td->childNodes->length == 0 && $td->nodeName == '#text' && trim($td->nodeValue)) {
						$price = trim($td->nodeValue);
					} else foreach($td->childNodes as $te) {
						if($te->nodeName != '#text') continue;
						$label[] = trim($te->nodeValue);
					}
				}
			}
			if(count($label)) {
				$labeltext = implode(',', $label);
				$labeltext = str_replace(', ', ',', $labeltext);
				$label = explode(',', $labeltext);
				$x = -1;
				$xsize = 56;
				echo '  ';
				foreach($label as $part) {
					if($x >= 0) {
						if($x + strlen($part) + 2 < $xsize) {
							echo ', ';
							$x += 2;
						} else {
							echo PHP_EOL . '  ';
							$x = 0;
						}
					}
					echo $part;
					$x += strlen($part);
				}
				echo PHP_EOL;
				if(trim($price)) {
					echo '  Preise: ' . $price;
					echo PHP_EOL;
					echo PHP_EOL;
				}
			}
		}
	}
	//echo PHP_EOL;
}
