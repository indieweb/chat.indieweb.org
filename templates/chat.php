<div id="join_prompt">
  <button type="button" id="join_btn">Join the Chat</button>
</div>

<div id="signin" class="hidden">
  enter nickname: <input type="text" id="nickname">
</div>

<div id="connection_status" class="hidden">
  <input type="text" readonly="readonly" id="connection_status_field">
</div>

<div id="chat" class="hidden">
  <input type="text" id="message" autocomplete="off">
  <span id="notify_control" class="hidden">
    <button type="button" id="notify_btn">Enable Notifications</button>
  </span>
</div>


<div id="irc_notice" class="hidden"><div class="pad">
  <button type="button" class="close" id="close_notice_btn">×</button>
  <span class="nick" id="irc_notice_nick"></span>
  <span class="text" id="irc_notice_text"></span>
</div></div>


<style type="text/css">
.hidden {
  display: none;
}
#join_prompt button {
  padding: 4px;
  font-size: 15px;
  background: #94dfef;
  border: 1px #78cee1 solid;
  border-radius: 4px;
}
#notify_control button {
  font-size: 15px;
  background: #ccc;
  border: 1px #999 solid;
  border-radius: 4px;
  float:right;
}
#notify_control button.enabled {
  border: 1px #78cee1 solid;
  background: #94dfef;
}
#connection_status_field {
  width: 300px;
}
#message {
  font-size: 15px;
  width: 400px;
}
#irc_notice {
  position: fixed;
  bottom: 60px;
  left: 20px;
  right: 20px;
  background: #f2dede;
  border: 2px #ebccd1 solid;
  color: #a94442;
  border-radius: 4px;
}
#irc_notice .pad {
  margin: 15px;
}
#irc_notice .nick {
  font-weight: bold;
}
#irc_notice .close {
  position: relative;
  top: -6px;
  right: -9px;
  border: 0;
  float: right;
  cursor: pointer;
  background: 0 0;
  -webkit-appearance: none;
  font-size: 21px;
  font-weight: 700;
  line-height: 1;
  color: #000;
  text-shadow: 0 1px 0 #fff;
  opacity: 0.2;
  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
}
#irc_notice .close:hover {
  opacity: 0.5;
}
</style>

<script>
document.getElementById('close_notice_btn').addEventListener('click', function(){
  document.getElementById('irc_notice').classList.add('hidden');
});


  var join_btn = document.getElementById('join_btn');
  var notify_btn = document.getElementById('notify_btn');
  var message_box = document.getElementById('message');
  var nick_field = document.getElementById('nickname');
  var status_field = document.getElementById('connection_status_field');
  var notify = false;
  var nickname;
  var nickname_regex = null;
  var nickname_self_regex = null;
  var connected = false;

  join_btn.addEventListener('click', function(){
    document.getElementById('join_prompt').classList.add('hidden');
    document.getElementById('signin').classList.remove('hidden');
    if(get_nick_from_cookie()) {
      nick_field.value = get_nick_from_cookie();
    }
    nick_field.focus();
    window.scrollTo(0,document.body.offsetHeight);

    var nick_key_listener = function(e) {
      if(e.keyCode == 13) {
        console.log("Sending to IRC: "+document.getElementById('message').value);
        if(!connected) {
          show_notice("connecting...","connecting to the chat room...");
        }
      }
    };
    nick_field.addEventListener("keypress", nick_key_listener);
    
  });

  notify_btn.addEventListener('click', function(){
    if(notify){
        notify = false;
        notify_btn.classList.remove('enabled');
        notify_btn.innerHTML = 'Enabled Notifications';
    } else {
        if (!("Notification" in window)) {
            alert("Notifications not supported on this browser.");
        } else if (Notification.permission === "granted") {
            notify = true;
            notify_btn.classList.add('enabled');
            notify_btn.innerHTML = 'Disable Notifications';
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission(function (permission) {
                if (permission === "granted") {
                    notify = true;
                    notify_btn.classList.add('enabled');
                    notify_btn.innerHTML = 'Disable Notifications';
                }
            });
        }
    }
  });

  nick_field.addEventListener('keypress', function(e){
    if(e.keyCode == 13) {
      document.getElementById('signin').classList.add('hidden');
      document.getElementById('chat').classList.remove('hidden');
      
      // status_field.value = "connecting...";

      nickname = nick_field.value;
      nickname_regex = new RegExp(nickname, "i");
      nickname_self_regex = new RegExp('^# \\d\\d:\\d\\d \\[?'+nickname, "i");
      document.getElementById('message').focus();

      var message_key_listener = function(e) {
        if(e.keyCode == 13) {
          console.log("Sending to IRC: "+message_box.value);
          if(!connected) {
            show_notice("connecting...","connecting to the chat room...");
          }
          send(message_box.value);
        }
      };
      message_box.addEventListener("keypress", message_key_listener);

      // Save this in a cookie
      document.cookie = "nickname=" + nickname;

      join(nickname);
    }
  });

function get_nick_from_cookie() {
  return document.cookie.replace(/(?:(?:^|.*;\s*)nickname\s*\=\s*([^;]*).*$)|^.*$/, "$1");
}

function show_notice(nick, text) {
  document.getElementById('irc_notice').classList.remove('hidden');
  document.getElementById('irc_notice_nick').innerHTML = nick;
  document.getElementById('irc_notice_text').innerHTML = text;
}
function hide_notice() {
  document.getElementById('irc_notice').classList.add('hidden');
  document.getElementById('irc_notice_nick').innerHTML = "";
  document.getElementById('irc_notice_text').innerHTML = "";
}
function check_alert(data){
  if(!connected) {
    console.log("Not connected. Got text: ");
    console.log(data);
    if(data.nick == nickname) {
      connected = true;
      console.log("Connected");
      hide_notice();
    }
  }
  if(notify){
    if(data.line.match(nickname_regex) && data.nick != nickname) {
      if (!("Notification" in window)) {
          console.log("Notifications not supported on this browser.");
      } else if (Notification.permission === "granted") {
          var notification = new Notification(text);
      } else if (Notification.permission !== 'denied') {
          Notification.requestPermission();
      }
    }
  }
}
function send(text) {
  xhr = new XMLHttpRequest();
  
  xhr.open('POST', encodeURI('/irc/send.php?action=input'));
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    if (xhr.status === 200) {
      console.log("sent");
      message_box.value = '';
    }
    else {
      alert('Request failed.  Returned status of ' + xhr.status);
    }
  };
  xhr.send('user_name=' + encodeURIComponent(nickname) + '&text=' + encodeURIComponent(text));
}
function join(nick) {
  xhr = new XMLHttpRequest();

  xhr.open('POST', encodeURI('/irc/send.php?action=join'));
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onload = function() {
    console.log("Got status "+xhr.status);
    if (xhr.responseText == 'connecting') {
      console.log("connecting...");
    } else if(xhr.responseText == 'connected') {
      connected = true;
      console.log("connected");
      hide_notice();
    } else {
      alert('Request failed.  Returned status of ' + xhr.status);
    }
  };
  xhr.send('user_name=' + encodeURIComponent(nickname));
}
</script>
