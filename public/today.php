<?php
include('inc.php');

$channel = $_GET['channel'];
$destination = ($channel == 'indieweb' ? '' : $channel.'/');
header('Location: ' . Config::$base . $destination . date('Y-m-d'));
