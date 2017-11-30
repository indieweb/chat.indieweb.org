<?php
include('inc.php');


if(!in_array($_GET['channel'], Config::supported_channels())) {
  header('HTTP/1.1 404 Not Found');
  die('channel not found');
}

if($_GET['channel'] == 'pdxbots' && $_SERVER['REMOTE_ADDR'] != '162.213.78.244') {
  die('forbidden');
}


# Pre-load variables required for header
$permalink = false;

loadUsers();
$timezones = loadTimezones();

# Get timezone of viewer from cookie
list($tzname, $tz) = getViewerTimezone();
$utc = new DateTimeZone('UTC');


# Get the start/end times for this day
$start = new DateTime($_GET['date'].' 00:00:00', $utc);
$start->setTimeZone($tz);
$date = clone $start;
$end = new DateTime($_GET['date'].' 23:59:59', $utc);
$end->setTimeZone($tz);

$start_utc = new DateTime($_GET['date'].' 00:00:00', $utc);
$end_utc = new DateTime($_GET['date'].' 23:59:59', $utc);

$dateTitle = $start_utc->format('Y-m-d');

$tmrw = new DateTime($_GET['date'].' 00:00:00', $utc);
$tmrw->add(new DateInterval('P1D'));
$tomorrow = $tmrw->format('Y-m-d');
$ystr = new DateTime($_GET['date'].' 00:00:00', $utc);
$ystr->sub(new DateInterval('P1D'));
$yesterday = $ystr->format('Y-m-d');
if($tmrw->format('U') > time()) $tomorrow = false;

$channel = '#'.$_GET['channel'];
$channel_link = Config::base_url_for_channel($channel);

if (!isAfterFirst($channel, $yesterday)) {
    $yesterday = false;
}
$channelName = $channel;

// #indiewebcamp channel was renamed to #indieweb on 2016-07-04
if($start->format('U') < 1467615600 && $channelName == '#indieweb') $channelName = '#indiewebcamp';


$db = new Quartz\DB('data/'.Config::logpath_for_channel($channel), 'r');
$results = $db->queryRange(clone $start_utc, clone $end_utc);



$noindex = true;
include('templates/header.php');
include('templates/header-bar.php');

# Render chat logs here
?>
<main>

<h2 class="date"><span class="channel-name"><?= $channelName ?></span> <?= $dateTitle ?></h2>

<div class="logs">
  <div id="top" class="skip"><a href="#bottom">jump to bottom</a></div>
  <?php if(isset($yesterday) && $yesterday): ?>
  <div class="hide-on-large-only center-align">
    <a href="./<?= $yesterday ?>" rel="prev" class="waves-effect waves-light btn">Prev</a>
  </div>
  <?php endif; ?>
  <div id="log-lines">
    <?php
    $lastday = $start;
    if($lastday->format('Y-m-d') != $start_utc->format('Y-m-c')) {
      echo '<div class="daymark">'.$start->setTimeZone($tz)->format('Y-m-d').' <span class="tz">'.$tzname.'</span></div>';
    }
    foreach($results as $line) {
      if($line->date->setTimeZone($tz)->format('Y-m-d') != $lastday->format('Y-m-d')) {
        echo '<div class="daymark">'.$line->date->setTimeZone($tz)->format('Y-m-d').' <span class="tz">'.$tzname.'</span></div>';
      }
      echo format_line($channel, $line->date, $tz, $line->data);
      $d = clone $line->date;
      $d->setTimeZone($tz);
      $lastday = $d;
    }
    // while($row=$logs->fetch(PDO::FETCH_OBJ)) {
    //   $date = DateTime::createFromFormat('U.u', sprintf('%.03f',$row->timestamp/1000));
    //   echo format_line($channel, $date, $tz, db_row_to_new_log($row));
    // }
    ?>
  </div>
  <?php if(isset($tomorrow) && $tomorrow): ?>
  <div class="hide-on-large-only center-align">
    <a href="./<?= $tomorrow ?>" class="waves-effect waves-light btn">Next</a>
  </div>
  <?php endif; ?>
  <div id="bottom" class="skip"><a href="#top">jump to top</a></div>
</div>

<?php if(!isset($tomorrow) || !$tomorrow): /* Set the channel name to activate realtime streaming, only when viewing "today" */ ?>
  <input id="active-channel" type="hidden" value="<?= Config::irc_channel_for_slug($_GET['channel']) ?>" style="display:none;"/>
<?php endif; ?>

<?php include('templates/footer-bar.php'); ?>

<script type="text/javascript" src="/assets/pushstream.js"></script>
<script type="text/javascript">/*<![CDATA[*/
  if(window.location.hash && window.location.hash != '#top' && window.location.hash != '#bottom') {
    var n = document.getElementById(window.location.hash.replace('#',''));
    n.classList.add('hilite');
  }
  window.addEventListener("hashchange", function(){
    var n = document.getElementsByClassName('line');
    Array.prototype.filter.call(n, function(el){ el.classList.remove('hilite') });
    var n = document.getElementById(window.location.hash.replace('#',''));
    n.classList.add('hilite');
  }, false);
/*]]>*/</script>
<?php if(!array_key_exists('timestamp', $_GET) && isset($date) && date('Y-m-d') == $date): ?>
<script type="text/javascript" src="/assets/log-streaming.js"></script>
<?php endif; ?>

</main>
<?php
include('templates/footer.php');
