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


loadUsers();

$tz = new DateTimeZone('UTC');
$html = format_line($params['channel']['name'], $date, $tz, json_decode($input));

$ch = curl_init('https://chat.indieweb.org/__/pub?id=chat');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$data = [];
$data['html'] = $html;
$data['channel'] = $params['channel']['name'];
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_exec($ch);
