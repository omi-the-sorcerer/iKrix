<?php
session_start();
session_write_close();

$time = $_SESSION["running"];



function onProfilePicture($from, $type, $data)
{
    if($type == "preview")
    {
        $filename = "../whatsapp/pictures/preview_" . $from . ".jpg";
        $fileurl = "/lib/whatsapp/pictures/preview_" . $from . ".jpg";
    }
    else
    {
        $filename = "../whatsapp/pictures/" . $from . ".jpg";
        $fileurl = "/lib/whatsapp/pictures/" . $from . ".jpg";
    }
    if(!file_exists($filename))
    {
        $fp = @fopen($filename, "w");
        if($fp)
        {
            fwrite($fp, $data);
            fclose($fp);
        }
    }
    session_start();
    $_SESSION["profilepic"] = $fileurl;
    session_write_close();
}

function numtoname($num) 
{ 
    require "../whatsapp/agend.php";

    if(array_key_exists($num, $numbers))
        return $numbers[$num];
    else
        return $num;
} 

function s($text)
{
    return str_replace("\n", "", htmlentities(strip_tags($text)));
}

function running($time)
{
    //Compare initial timestamp in session
    //and current timestamp in session. This
    //timestamp is updated each time index.php
    //is called (page is refreshed). This will
    //kill the old socket.php processes.
    session_start();
    $running = $_SESSION["running"];
    if($running != $time)
    {
        //index.php refreshed by user
        die();
    }
    session_write_close();
    return true;//continue running
}

function onGetImage($mynumber, $from, $id, $type, $t, $name, $size, $url, $file, $mimetype, $filehash, $width, $height, $preview)
{
    //save thumbnail
    $previewuri = "../whatsapp/media/thumb_" . $file;
    $fp = @fopen($previewuri, "w");
    if($fp)
    {
        fwrite($fp, $preview);
        fclose($fp);
    }
    
    //download and save original
    $data = file_get_contents($url);
    $fulluri = "../whatsapp/media/" . $file;
    $fp = @fopen($fulluri, "w");
    if($fp)
    {
        fwrite($fp, $data);
        fclose($fp);
    }
    
    //format message
    $msg = "<a href='/lib/whatsapp/media/".$file."' target='_blank'><img src='/lib/whatsapp/media/thumb_".$file."' /></a>";
    
    //insert message
    session_start();
    $in = $_SESSION["inbound"];
    $in[]["message"] = $msg;
    $in[]["time"] = date("Y-m-d H:i:s", $t);

    $from = substr($from[0], 2);

    $in[]["from"] = $from;
    $in[]["name"] = convert_emoji($name);
    $_SESSION["inbound"] = $in;
    session_write_close();
}

require_once '../whatsapp/whatsprot.class.php';
require "emoji.php";
$nick = @$_POST["nick"];

$w = new WhatsProt($nick);
$w->EventManager()->bind("onGetImage", "onGetImage");
$w->eventManager()->bind("onProfilePicture", "onProfilePicture");
$w->Connect();
$w->LoginWithPassword();


$initial = @$_POST["initial"];

while(running($time))
{
    session_start();
    $phone = $_SESSION["phone"];
    if($initial && $phone != '')
    {
        $w->getProfilePicture("34".$phone);
    }
    session_write_close();

    $w->PollMessages();
    
    running($time);

    session_start();
    $outbound = $_SESSION["outbound"];
    $_SESSION["outbound"] = array();
    session_write_close();
    if(count($outbound) > 0)
    {
        foreach($outbound as $message)
        {
            if($message["type"] == "message")
            {
                $w->Message('34'.$message["target"], $message["body"]);  
                $w->PollMessages();
            }
            elseif($message["type"] == "image")
            {
                $w->MessageImage('34'.$message["target"], $message["body"]);
                $w->PollMessages();
            }
        }
    }
    
    //check for received messages:
    $messages = $w->GetMessages();
    if(count($messages) > 0)
    {
        session_start();
        $inbound = $_SESSION["inbound"];
        $_SESSION["inbound"] = array();
        foreach($messages as $message)
        {
            $data = @$message->getChild("body")->_data;

            $time = @$message->_attributeHash['t'];
            $time = date("Y-m-d H:i:s", $time);

            $from = @$message->_attributeHash['from'];
            $from = explode('@', $from);
            $from = substr($from[0], 2);

            $name = @$message->getChild("notify")->_attributeHash['name'];

            $lline = file("wlog");
            if($data != null && $data != '' && $data != $lline[count($lline)-1])
            {
                $inbound[]["message"] = convert_emoji($data);
                $inbound[]["time"] = $time;
                $inbound[]["from"] = $from;
                $inbound[]["name"] = convert_emoji($name);
                fwrite(fopen("wlog", "w"), $data);
            }
        }
        $_SESSION["inbound"] = $inbound;
        session_write_close();
    }
}