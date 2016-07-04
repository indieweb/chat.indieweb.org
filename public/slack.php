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
  <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
</head>
<body>
  
  <div id="container">
  <?php
    
    
if(!array_key_exists('email', $_POST)):
  ?>
  <h1>Welcome to IndieWebCamp!</h1>
  <p>Enter your email address to be invited to the Slack gateway!</p>
  <p>Slack is just one way you can join the IndieWebCamp chat room. You can also join via <a href="https://indiewebcamp.com/IRC">IRC</a> on freenode.net, as well as from our <a href="https://indiewebcamp.com/irc/today#bottom">web interface</a>.</p>
  <form action="/slack" method="post" class="pure-form">
    <label>Email:</label>
    <input type="email" name="email" placeholder="" />
    <p><button type="submit" class="pure-button pure-button-primary">Join</button></p>
  </form>
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



  </div>
</body>
</html>
<?php
