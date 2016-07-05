<?php
include('inc.php');

$channel = $_GET['channel'];
$destination = ($channel == 'indieweb' ? '' : $channel.'/');

list($tzname, $tz) = getViewerTimezone();

$date = new DateTime();
$date->setTimeZone($tz);

header('Location: ' . Config::$base . $destination . $date->format('Y-m-d'));
