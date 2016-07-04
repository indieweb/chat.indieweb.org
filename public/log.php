<?php
include('inc.php');

$input = file_get_contents('php://input');
$params = json_decode($input, true);

unset($params['author']['pronouns']);
unset($params['match']);
unset($params['response_url']);

$date = DateTime::createFromFormat('U.u', $params['timestamp']);

$db = new Quartz\DB('data/'.$params['server'].'/'.$params['channel']['name'], 'w');
$db->add($date, $params);
$db->close();
