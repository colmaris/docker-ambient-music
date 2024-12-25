<?php
/*
Reads a list of all the mp3 files in the subdirectories and writes a playlist.m3u file.
Use lib getid3() (http://getid3.sourceforge.net/)
Doc: http://getid3.sourceforge.net/source/?t=structure.txt

*/
error_reporting(E_ALL); ini_set('display_errors', '1');

header('Content-Type: text/plain;charset=UTF-8');
if (!file_exists('getid3/getid3.php')) { die('ERROR: getid3 library is required. Download it from http://getid3.sourceforge.net/'); }
require_once 'getid3/getid3.php';

function startsWith($haystack, $needle) { return strpos($haystack, $needle) === 0; }
function endsWith($haystack, $needle) { return substr($haystack, -strlen($needle)) == $needle; }

/**
 * Flattens an array.
 */
function array_flat($array) {
  $tmp = Array();
  foreach($array as $a) {
    if(is_array($a)) {
      $tmp = array_merge($tmp, array_flat($a));
    }
    else {
      $tmp[] = $a;
    }
  }
  return $tmp;
}

/**
 * Recurses subdirectories and returns the list of all files contained within
 * as a flat array (1 item per file, with path)
 */
function getFilesFromDir($dir) {
  $files = array();
  if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            if(is_dir($dir.'/'.$file)) {
                $dir2 = $dir.'/'.$file;
                $files[] = getFilesFromDir($dir2);
            }
            else {
              $files[] = $dir.'/'.$file;
            }
        }
    }
    closedir($handle);
  }
  $tmp = array_flat($files);
  natcasesort($tmp); // case insentitive sort.
  return $tmp;
}

$GLOBALS['TOTALTIME']=0;

/**
 * Builds a EXTINF line (part of m3u file format)
 * Example: #EXTINF:333,Brian Eno - Lantern Marsh
 * (333 is the duration in seconds, then Artist - Title)
 */
function makeEXTInfLine($filename)
{
    $getID3 = new getID3;
    $fileinfo = $getID3->analyze($filename);
    getid3_lib::CopyTagsToComments($fileinfo); // To get id3v1 and id3v2 tags at the same place.
    $duration = (string)floor($fileinfo['playtime_seconds']);
    $GLOBALS['TOTALTIME'] += $fileinfo['playtime_seconds'];
    $artist = $fileinfo['comments']['artist'][0];
    $title = $fileinfo['comments']['title'][0];
    return '#EXTINF:'.$duration.','.$artist.' - '.$title;
}
/**
 * Builds a m3u playlist from all mp3 files located in a directory.
 * Returns a properly formatted m3u file.
 * Input: $dir : directory to scan.
 *        $baseurl : Base URL where this directory is served.
 * Output: (string)  the resulting m3u file.
 */
function buildM3u($dir,$baseurl)
{
    $lines = Array();
    foreach(getFilesFromDir($dir) as $filename)
    {
        if (pathinfo($filename, PATHINFO_EXTENSION)=='mp3')
        {
			echo "Processing $filename\n";
            $lines[] = makeEXTInfLine($filename);
            $lines[] = $baseurl.substr($filename, strlen($dir));
        }
    }
    return implode("\n",$lines);
}

$data = buildM3u('.','https://ambient.colmaris.fr');
file_put_contents('playlist.m3u',$data);
echo 'Playlist regénérée. Durée totale (en secondes): ',$GLOBALS['TOTALTIME'];
?>
