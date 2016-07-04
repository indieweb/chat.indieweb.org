<?php
include('inc.php');

setcookie('timezone_view', $_POST['tz'], (time()+86400*365));
header('Location: '.$_POST['location']);

