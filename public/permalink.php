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
$channel_link = Config::base_url_for_channel('#'.$_GET['channel']);
$timestamp = $_GET['timestamp'];


$query = db()->prepare('SELECT * FROM irclog 
  WHERE channel=:channel AND timestamp = :timestamp AND hide=0');
$query->bindParam(':channel', $query_channel);
$query->bindValue(':timestamp', floor($timestamp/1000));
$query->execute();
$current = false;
while($q = $query->fetch(PDO::FETCH_OBJ))
  $current = $q;

if(!$current)
  die('not found');

$user = userForNick($current->nick);
if($user) {
  $userUrl = @$user->properties->url[0];
}

$date = DateTime::createFromFormat('U.u', sprintf('%.03f',$current->timestamp/1000));

$dateTitle = $date->format('Y-m-d');

$channelName = $channel;
if(($timestamp/1000000) < 1467615600 && $channelName == '#indieweb') $channelName = '#indiewebcamp';

header('Last-Modified: '.date('D, d M Y H:i:s T', $timestamp/1000000));
header('Cache-Control: max-age=2592000');

if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
  if(strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= floor($timestamp/1000000)) {
    header('HTTP/1.1 304 Not Modified');
    die();
  }
}

include('templates/header.php');
include('templates/header-bar.php');
?>
<main>
  <div class="logs">
    <div id="log-lines" class="featured">
      <?= format_line($channel, $date, $tz, db_row_to_new_log($current)) ?>
    </div>
  </div>
</main>
<?php

include('templates/footer.php');
