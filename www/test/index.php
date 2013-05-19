<?
require "../lib/whatsapp/whatsprot.class.php";

function onProfilePicture($from, $type, $data)
{
    if($type == "preview")
    {
        $filename = "/preview_" . $from . ".jpg";
    }
    else
    {
        $filename = "/" . $from . ".jpg";
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
    print("<img src='/test/".$from.".jpg'>");
}

$w = new WhatsProt();
$w->eventManager()->bind("onProfilePicture", "onProfilePicture");
$w->Connect();
$w->LoginWithPassword();
$w->GetProfilePicture("34622266623", true);
?>