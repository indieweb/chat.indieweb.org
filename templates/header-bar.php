<ul class="side-nav fixed" id="slide-out">
  <li><div class="userView">
    <img src="/assets/user-bkg.jpg" class="background"/>
    <a href="#"><img class="circle" src="/assets/logo/<?= Config::logo_for_channel($channel); ?>"/></a>
    <a><span class="white-text name" style="font-size: 18pt;"><?= $channelName ?></span></a>
    <a><span class="white-text email"><?= $dateTitle ?></span></a>
  </div></li>

  <li style="display: flex; flex-direction: row;">
    <div style="flex: 1 1;">
      <?php if(isset($yesterday) && $yesterday): ?>
        <a href="./<?= $yesterday ?>" rel="prev">Prev</a>
      <?php else: ?>
        <a class="disabled">Prev</a>
      <?php endif; ?>
    </div>
    <div style="flex: 1 1;">
      <?php if(isset($tomorrow) && $tomorrow): ?>
        <a href="./<?= $tomorrow ?>" rel="next">Next</a>
      <?php else: ?>
        <a class="disabled">Next</a>
      <?php endif; ?>
    </div>
  </li>

  <li class="divider"></li>

  <?php foreach(Config::related_channels($channel) as $c): ?>
    <li class="channel <?= ($channel == '#'.$c ? 'current' : '') ?>" data-channel="<?= Config::irc_channel_for_slug($c) ?>">
      <a href="<?= Config::base_url_for_channel($c) ?>">#<?= $c ?></a>
    </li>
  <?php endforeach; ?>

  <li class="divider"></li>

  <li class="search">
    <form action="https://indiechat.search.cweiske.de/" method="get">
      <div style="position:relative">
        <input type="text" name="q" id="search" placeholder="Search"/>
        <button class="material-icons" type="submit">search</button>
      </div>
    </form>
  </li>
  <li>
    <form action="/set-timezone.php" method="post" id="set-timezone-form">
      <div class="input-field col s12">
        <select class="browser-default" id="set-timezone" name="tz" onchange="document.getElementById('set-timezone-form').submit()">
          <?php foreach($timezones as $t): ?>
            <option value="<?= $t ?>" <?= $t == $tzname ? 'selected="selected"' :'' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="location" value="<?= $_SERVER['REQUEST_URI'] ?>"/>
      </div>
    </form>
  </li>

</ul>

<div class="navbar-fixed">
  <nav>
    <div class="nav-wrapper">
      <a href="#" class="brand-logo"><?= $channelName ?></a>
      <a href="#" data-activates="slide-out" class="button-collapse"><abbr title="menu">≡</abbr></a>

      <ul class="right">
        <li>
          <?php if(isset($yesterday) && $yesterday): ?>
            <a href="./<?= $yesterday ?>" rel="prev"><abbr title="Previous">←</abbr></a>
          <?php else: ?>
            <a class="disabled"><abbr title="Previous">←</abbr></a>
          <?php endif; ?>
        </li>
        <li>
          <?php if(isset($tomorrow) && $tomorrow): ?>
            <a href="./<?= $tomorrow ?>" rel="next"><abbr title="Next">→</abbr></a>
          <?php else: ?>
            <a class="disabled"><abbr title="Next">→</abbr></a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </nav>
</div>

<script>
$(function(){
  $(".button-collapse").sideNav();
  $("li.channel").each(function(i,ch){
    if(channel_unread($(ch).data('channel'))) {
      $(ch).addClass('activity');
    }
  });
  channel_read($("#active-channel").val());
});
</script>
