<div id="indieweb-header">

  <a class="item" href="https://indieweb.org/"><img src="/assets/indiewebcamp.svg" class="logo"></a>

  <a class="item" href="<?= $channel_link ?>"><?= $channel ?></a>
  <a class="item" href="<?= $channel_link ?><?= $date->format('Y-m-d') ?>"><?= $dateTitle ?></a>

  <ul class="right">
    <!--
    <li>
      <form action="http://www.google.com/search" method="get" style="margin-bottom: 0;">
        <input type="text" name="q" placeholder="Search">
        <input type="submit" value="Search">
        <input type="hidden" name="as_sitesearch" value="indiewebcamp.com/irc">
      </form>
    </li>
    -->
    <li>
      <form action="/set-timezone.php" method="post" id="set-timezone-form">
        <select id="set-timezone" name="tz" onchange="document.getElementById('set-timezone-form').submit()">
          <?php foreach($timezones as $t): ?>
            <option value="<?= $t ?>" <?= $t == $tzname ? 'selected="selected"' :'' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="location" value="<?= $_SERVER['REQUEST_URI'] ?>">
      </form>
    </li>
    <li>
      <?php if(isset($yesterday)): ?>
        <a href="./<?= $yesterday ?>" rel="prev">Prev</a>
      <?php else: ?>
        <span class="disabled">Prev</span>
      <?php endif; ?>
    </li>
    <li>
      <?php if(isset($tomorrow)): ?>
        <a href="./<?= $tomorrow ?>" rel="next">Next</a>
      <?php else: ?>
        <span class="disabled">Next</span>
      <?php endif; ?>
    </li>
  </ul>

</div>

