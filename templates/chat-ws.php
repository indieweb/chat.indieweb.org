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
#connection_status_field, #message {
  width: 300px;
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

if("WebSocket" in window) {
  var join_btn = document.getElementById('join_btn');
  var notify_btn = document.getElementById('notify_btn');
  var message_box = document.getElementById('message');
  var nick_field = document.getElementById('nickname');
  var status_field = document.getElementById('connection_status_field');
  var notify = false;
  var nickname_regex = null;
  var nickname_self_regex = null;

  join_btn.addEventListener('click', function(){
    document.getElementById('join_prompt').classList.add('hidden');
    document.getElementById('signin').classList.remove('hidden');
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
      console.log("Connecting...");

      document.getElementById('signin').classList.add('hidden');
      document.getElementById('connection_status').classList.remove('hidden');
      status_field.value = "connecting...";

      var ws = new WebSocket(window.location.origin.replace(/https?/,"wss")+":8092");

      var message_key_listener = function(e) {
        if(e.keyCode == 13) {
          console.log("Sending to IRC: "+document.getElementById('message').value);
          ws.send(JSON.stringify({"message": message_box.value}));
          message_box.value = "";
        }
      };

      ws.onopen = function(e) {
    
        var nickname = nick_field.value;
        nickname_regex = new RegExp(nickname, "i");
        nickname_self_regex = new RegExp('^# \\d\\d:\\d\\d '+nickname, "i");
        ws.send(nickname);
        console.log('websockets connection established, waiting to join channel');

        message_box.addEventListener("keypress", message_key_listener);
      }
      ws.onmessage = function(e) {
        var data = JSON.parse(e.data);
        console.log(data);
        if(data.type == "connected") {
          document.getElementById('connection_status').classList.add('hidden');
          document.getElementById('chat').classList.remove('hidden');
          if ("Notification" in window) {
            document.getElementById('notify_control').classList.remove('hidden');
          }
        } else if(data.type == "disconnected") {
          disconnected();
          message_box.removeEventListener("keypress", message_key_listener);
          ws.close();
        } else if(data.type == "notice") {
          show_notice(data.nick, data.text);
        } else if(data.description) {
          status_field.value = data.description
        }
      }
      ws.onclose = function(e) {
        console.log("Websocket disconnected");
        message_box.removeEventListener("keypress", message_key_listener);
        disconnected();
      }
    }
  });
}

function disconnected() {
  notify = false;
  notify_btn.classList.remove('enabled');
  notify_btn.innerHTML = 'Enabled Notifications';
  document.getElementById('join_prompt').classList.remove('hidden');
  document.getElementById('signin').classList.add('hidden');
  document.getElementById('connection_status').classList.add('hidden');
  document.getElementById('chat').classList.add('hidden');
  document.getElementById('notify_control').classList.add('hidden');
}

function show_notice(nick, text) {
  document.getElementById('irc_notice').classList.remove('hidden');
  document.getElementById('irc_notice_nick').innerHTML = nick;
  document.getElementById('irc_notice_text').innerHTML = text;
}
function check_alert(text){
    if(notify){

        if(text.match(nickname_regex) && !text.match(nickname_self_regex)) {

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
</script>
