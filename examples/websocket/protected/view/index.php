<style type="text/css">
   
    .chat_wrapper {
        width: 500px;
        margin-right: auto;
        margin-left: auto;
        background: #CCCCCC;
        border: 1px solid #999999;
        padding: 10px;
        font: 12px 'lucida grande',tahoma,verdana,arial,sans-serif;
    }
    .chat_wrapper .message_box {
        background: #FFFFFF;
        height: 150px;
        overflow: auto  ;
        padding: 10px;
        border: 1px solid #999999;
    }
    .chat_wrapper .panel input{
        padding: 2px 2px 2px 5px;
    }
    .system_msg{color: #BDBDBD;font-style: italic;}
    .user_name{font-weight:bold;}
    .user_message{color: #88B6E0;}
   
</style>
<?php
$colours = array('007AFF', 'FF7000', 'FF7000', '15E25F', 'CFC700', 'CFC700', 'CF1100', 'CF00BE', 'F00');
$user_colour = array_rand($colours);
/* @var $ObjResponse Html */
$ObjResponse->addlink_js('{src}jquery-1.7.2.min.js');
?>



<script language="javascript" type="text/javascript">
    $(document).ready(function () {
        //create a new WebSocket object.
        //var wsUri = "ws://192.168.43.152:12345/MiChat.php";
        var wsUri = "ws://127.0.0.1:12345/MiChat.php?hola=1&p=hola";
        var websocket = new WebSocket(wsUri);

        websocket.onopen = function (ev) { // connection is open 
            $('#message_box').append("<div class=\"system_msg\">Connected!</div>"); //notify user
        };
        $('#message').keyup(function (e)
        {
           var mymessage = $('#message').val(); //get message text
            var myname = $('#name').val(); //get user name

            if (myname == "") { //empty name?
                alert("Enter your Name please!");
                return;
            }
            if (mymessage == "") { //emtpy message?
                alert("Enter Some message Please!");
                return;
            }

            //prepare json data
            var msg = {
                message: mymessage,
                name: myname,
                color: '<?php echo $colours[$user_colour]; ?>'
            };
            //convert and send data to server
             if(e.which==13)
            {
                 websocket.send(JSON.stringify(msg));
              $('#message').val('');
            }
           
        });
        $('#send-btn').click(function () { //use clicks message send button	
            var mymessage = $('#message').val(); //get message text
            var myname = $('#name').val(); //get user name

            if (myname == "") { //empty name?
                alert("Enter your Name please!");
                return;
            }
            if (mymessage == "") { //emtpy message?
                alert("Enter Some message Please!");
                return;
            }

            //prepare json data
            var msg = {
                message: mymessage,
                name: myname,
                color: '<?php echo $colours[$user_colour]; ?>'
            };
            //convert and send data to server
            websocket.send(JSON.stringify(msg));
              $('#message').val('');
        });

        //#### Message received from server?
        websocket.onmessage = function (ev) {
            console.log(ev);
            var msg = $.parseJSON(ev.data)//PHP sends Json data
            var type = msg.type; //message type
            var umsg = msg.message; //message text
            var uname = msg.name; //user name
            var ucolor = msg.color; //color

            if (type == 'usermsg')
            {
                var box=$("<div><span class=\"user_name\" style=\"color:#" + ucolor + "\">" + uname + "</span> : <span class=\"user_message\">" + umsg + "</span></div>");
                box.appendTo('#message_box');
                box.scrollTop(-30);
            }
            if (type == 'system')
            {
                $('#message_box').append("<div class=\"system_msg\">" + umsg + "</div>");
            }
           //reset text
        };
        
        websocket.onerror = function (ev) {
        console.log(ev);
            $('#message_box').append("<div class=\"system_error\">Error  - " + ev.data + "</div>");
        };
        websocket.onclose = function (ev) {
            $('#message_box').append("<div class=\"system_msg\">Coneccion Cerrada</div>");
        };
    });
</script>
<h1 style="text-align: center;">Mi Chat</h1>
<div class="chat_wrapper">
    <div class="message_box" id="message_box"></div>
    <div class="panel">
        <input type="text" name="name" id="name" placeholder="Tu Nombre" maxlength="10" style="width:20%"  />
        <input type="text" name="message" id="message" placeholder="Mensaje" maxlength="80" style="width:60%" />
        <input type="submit" id="send-btn" value="enviar" style="width: 65px;">
    </div>
</div>
