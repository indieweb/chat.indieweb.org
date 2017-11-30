<?php
include('inc.php');

if(!in_array($_GET['channel'], Config::supported_channels())) {
  header('HTTP/1.1 404 Not Found');
  die('channel not found');
}

# Pre-load variables required for header
$permalink = true;

loadUsers();
$timezones = loadTimezones();

# Get timezone of viewer from cookie
list($tzname, $tz) = getViewerTimezone();
$utc = new DateTimeZone('UTC');


$query_channel = Config::irc_channel_for_slug($_GET['channel'], $_GET['timestamp']);
$channel = '#'.$_GET['channel'];
$channelName = $channel;
$channel_link = Config::base_url_for_channel('#'.$_GET['channel']);
$timestamp = $_GET['timestamp'];


$date = DateTime::createFromFormat('U.u', sprintf('%.06f',$timestamp/1000000));

$db = new Quartz\DB('data/'.Config::logpath_for_channel($channel), 'r');
$line = $db->getByDate($date);

if(!$line)
  die('not found');

$dateTitle = $date->format('Y-m-d');


header('Last-Modified: '.date('r', $timestamp/1000000));
header('Cache-Control: max-age=2592000');

if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
  if(strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= floor($timestamp/1000000)) {
    header('HTTP/1.1 304 Not Modified');
    die();
  }
}

if($line->type == 'join') {
    $noindex = true;
}

$current = $line->data;
if($current->author && property_exists($current->author, 'url') && $current->author->url) {
  $userUrl = $current->author->url;
}

include('templates/header.php');
include('templates/header-bar.php');
?>
<main>
  <div class="logs">
    <div id="log-lines" class="featured">
      <div class="daymark"><?= $date->setTimeZone($tz)->format('Y-m-d') ?> <span class="tz"><?= $tzname ?></span></div>
      <?= format_line($channel, $date, $tz, $line->data) ?>
    </div>
  </div>
</main>
<?php

include('templates/footer.php');
