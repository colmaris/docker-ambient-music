<?php

/* Generates a minimal xspf file from a .m3u file
   This code is in the public domain.
   Author: sebsauvage at sebsauvage dot net

   Warning: No error control (I create clean m3u files), no proper utf-8 handling.
*/
header('Content-Type: text/plain; charset=utf-8'); // We use UTF-8 for proper international characters handling.

// Tells if a string starts with a substring or not.
function startsWith($haystack,$needle,$case=true) {
    if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
    return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
}

function buildTrackXspf($artist,$tracktitle,$duration,$url)
{
	return "<track>\n<location>".htmlspecialchars(trim($url))."</location>\n<title>".htmlspecialchars(trim($tracktitle))."</title>\n<creator>".htmlspecialchars(trim($artist))."</creator>\n<duration>".$duration."000</duration>\n</track>";
}

$lines = explode("\n",file_get_contents('playlist.m3u'));
$currentline = 0;
$nblines = count($lines);

/* Example line:
   #EXTINF:404,Jon Hopkins - The End
           ^   ^             ^ track title
           ^   ^ artist name
           ^ duration (in seconds)
   The next line contains the URL.

This has to be transformed to:

    <track><location>URL</location><title>Tracktitle</title><creator>Artist</creator><duration>duration</duration></track>
*/
$tracks = array();
while ($currentline<$nblines ) {
	$line = $lines[$currentline];
	if (startsWith($line,'#EXTINF:')) {
		$data = substr($line, 8); // Remove #EXTINF:

		// Extract duration (in seconds)
		$j = strpos($data,',');
		$duration = intval(substr($data, 0, $j));
		$data = substr($data, $j+1);

		// Extract artist and track title
		$k = strpos($data, ' - ');
		$artist = substr($data, 0, $k);
		$tracktitle = substr($data, $k+3);

		// Get URL in next line
		$url = $lines[$currentline+1];

		$currentline = $currentline + 1;
		$tracks[] = buildTrackXspf($artist,$tracktitle,$duration,$url); // Build XSPF XML for this track
	}
	$currentline = $currentline + 1;
}

shuffle($tracks);


echo <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<playlist xmlns="http://xspf.org/ns/0/" version="1">
<title>Alternative musics for Minecraft gameplay</title>
<trackList>

XML;
foreach ($tracks as $track) {
	echo $track;
	echo "\n";
}
echo <<<XML
</trackList>
</playlist>	
XML;
?>
