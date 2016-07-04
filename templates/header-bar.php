<div id="indieweb-header">

  <a class="item" href="https://indieweb.org/"><img src="/assets/indiewebcamp.svg" class="logo"></a>

  <a class="item" href="<?= $channel_link ?>"><?= $channel ?></a>

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
      <?php if($yesterday): ?>
        <a href="./<?= $yesterday ?>" rel="prev">Prev</a>
      <?php else: ?>
        <span class="disabled">Prev</span>
      <?php endif; ?>
    </li>
    <li>
      <?php if($tomorrow): ?>
        <a href="./<?= $tomorrow ?>" rel="next">Next</a>
      <?php else: ?>
        <span class="disabled">Next</span>
      <?php endif; ?>
    </li>
  </ul>

</div>

<style type="text/css">
#indieweb-header {
  height: 30px;

  /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#cccccc+1,cccccc+23,eeeeee+99 */
  background: #cccccc; /* Old browsers */
  background: -moz-linear-gradient(top, #cccccc 1%, #cccccc 23%, #eeeeee 99%); /* FF3.6-15 */
  background: -webkit-linear-gradient(top, #cccccc 1%,#cccccc 23%,#eeeeee 99%); /* Chrome10-25,Safari5.1-6 */
  background: linear-gradient(to bottom, #cccccc 1%,#cccccc 23%,#eeeeee 99%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */

  border-bottom: #ddd 1px solid;
}
#indieweb-header .logo {
  height: 26px;
  margin-top: 2px;
  margin-left: 8px;
}
.bottombar { 
  padding-top: 10px;
  padding-bottom: 10px;
  position: fixed;
  bottom: 0;
  width: 100%;
}
#indieweb-header .item {
  float: left;
  margin: 0;
  padding: 0;
  line-height: 26px;
  margin-right: 20px;
}
#set-timezone-form {
  padding-top: 6px;
}
#indieweb-header ul.right {
  float: right;
  list-style-type: none;
  margin: 0;
  padding: 0;
  margin-right: 10px;
}
#indieweb-header ul.right li {
  float: left;
  margin-left: 20px;
  line-height: 26px;
  font-size: 15px;
}
#indieweb-header .disabled {
  color: #999;
}
</style>
