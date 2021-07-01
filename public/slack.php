<?php
include('inc.php');
?>
<html>
<head>
  <title>IndieWebCamp Slack Setup</title>
  <style type="text/css">
    body {
      color: rgb(127, 140, 141);
      font-family: sans-serif;
    }
    #container {
      max-width: 600px;
      margin: 0 auto;
    }
    h1 {
      font-size: 36px;
      font-weight: bold;
    }
    .large {
      font-size: 1.5em;
    }
    .small {
      font-size: 0.8em;
    }
    a {
      color: #3979b5;
    }
    a:hover {
      color: #4c8fcd;
    }
  </style>
</head>
<body>
  
  <div id="container">
  <?php
    
    
if(!array_key_exists('email', $_POST)):
  ?>
  <h1>Welcome to IndieWebCamp!</h1>
  <p>Enter your email address to be invited to the Slack gateway!</p>
  <p>Slack is just one way you can join the IndieWebCamp chat room. You can also join via <a href="https://indieweb.org/discuss">IRC</a> on freenode.net, as well as from our <a href="https://chat.indieweb.org/">web interface</a>.</p>
  <form action="/slack" method="post" class="pure-form">
    <label>Email:</label>
    <input type="email" name="email" placeholder="" />
    <p><button type="submit" class="pure-button pure-button-primary" style="font-size: 19px; padding: 3px 20px; border: 1px #ccc solid; border-radius: 3px;">Join</button></p>
  </form>
  <p>Please note that all our public Slack channels are bridged to IRC and archived permanently on the web at <a href="https://chat.indieweb.org/">chat.indieweb.org</a>. All contributions to the IndieWeb chat and wiki are made under a CC0 license.</p>
  <?php
  die();
endif;


$ch = curl_init('https://indiewebcamp.slack.com/api/users.admin.invite');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
  'email' => $_POST['email'],
  'token' => Config::$slack_token,
  'set_active' => 'true'
]));
$result = curl_exec($ch);

?>
  <h1>Check your email!</h1>
  <p>You should have an invitation in your inbox now!</p>
  <p>Check your spam folder if you don't see it at first. The email will come from Slack.</p>

<!--
<?= $result ?>
-->

  </div>
</body>
</html>
<?php
