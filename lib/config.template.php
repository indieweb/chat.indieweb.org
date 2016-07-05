<?php
class Config {
  public static $base = 'http://chat.indieweb.dev/';
  public static $logpath = './data/freenode/';

  public static $dbhost = '127.0.0.1';
  public static $dbname = 'logs';
  public static $dbuser = 'root';
  public static $dbpass = '';

  public static $secret = '';

  public static $slack_token = 'xoxp-...';

  public static function base_url_for_channel($channel) {
    if($channel == '#indiewebcamp' || $channel == '#indieweb')
      return Config::$base;
    else
      return Config::$base . str_replace('indieweb-','',trim($channel,'#')) . '/';
  }

  public static function irc_channel_for_slug($slug) {
    if($slug == '')
      return '#indieweb';
    else
      return '#indieweb-'.$slug;
  }
}
