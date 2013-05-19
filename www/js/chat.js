var target;
var nick;

function Crowsup()
{
	this.listen = Listen;
	this.get = getMessages;
	this.send = sendMessage;
	this.phone = changephone;
    this.upload = uploadAjax;
}

function changephone(phonetochange, nickname) {
	target = phonetochange;
	nick = nickname;
    $.ajax({
      url: '../lib/functions/crowsup.php',
      type: 'POST',
      dataType: 'html',
      cache: false,
      data: {phone: target, method: "changephone"}
    });
    
}

function Listen(initial)
{
    $.ajax({
        url: "../lib/functions/socket.php",
        cache: false,
        dataType: "html",
        timeout: 70000,
        method: "POST",
        data: {
            initial: true,
            nick: nick
        }
        }).done(function(data) {
            setTimeout(function() {Listen(true)}, 600);
        });
    
}

function getMessages()
{
    $.ajax({
        url: "../lib/functions/crowsup.php",
        cache: false,
        dataType: "json",
        method: "POST",
        data: {
                method: "pollMessages",
            }}).done(function(data) {
                if(data)
                {
                    if(data.profilepic != "")
                    {
                        $("#profilepic").attr("src", data.profilepic);
                    }
                    for(var i in data.messages)
                    {
                        addMessage(data.messages[i], data.phones[i], data.names[i], data.times[i]);
                    }
                }
                setTimeout(function() {getMessages()}, 600);
    });
}

function addMessage(message, phone, name, time)
{
	if (time == '') {
		$("#rec").append($("<div class=\"arrowText arrowRight\"><span class=\"name\">" + name + " -> " + phone + "</span>" + message + "</div>\n"));
	} else{
		$("#rec").append($("<div class=\"arrowText arrowLeft\" title=\"" + phone + "\"><span class=\"name\">" + name + "</span>" + message + "<span class='time'>" + time + "</span></div>\n"));
	};
    document.getElementById('rec').scrollTop = document.getElementById('rec').scrollHeight;
}

function sendMessage(message)
{
    if(message != '')
    {
        
        $.ajax({
            url: "../lib/functions/crowsup.php",
            cache: false,
            dataType: "html",
            method: "POST",
            data: {
                    method: "sendMessage",
                    target: target,
                    message: message,
                    nick: nick
                }}).done(function(data){
                addMessage(data, target, nick, '');
            });
    }
}

function uploadAjax()
{
    var inputFileImage = document.getElementById("archivoImage");
    var file = inputFileImage.files[0];
    var data = new FormData();
    data.append('archivo',file);
    var url = "../lib/functions/upload.php";
    $.ajax({
            url:url,
            type:'POST',
            contentType:false,
            data:data,
            processData:false,  
            cache:false}).done(function(data){
                sendMedia('image', data);
            });
     
}

function sendMedia(type, url)
{
    image = url.substr(8);
    addMessage("<a href='" + image + "' target='_blank'><img src='" + image + "' width='200'/></a>", target, nick, "");
    $.ajax({
        url: "../lib/functions/crowsup.php",
        cache: false,
        dataType: "html",
        method: "POST",
        data: {
                method: "sendMedia",
                type: type,
                target: target,
                message: url,
                nick: nick
            }});
}