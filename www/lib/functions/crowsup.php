<?php
require_once '../functions/emoji.php';

session_start();
class JSONResponse
{
    public $messages = array();
    public $profilepic = "";
    public $times = array();
    public $phones = array();
    public $names = array();
}
$method = $_POST["method"];
switch($method)
{
    case "sendMessage":
        $target = $_POST["target"];
        $message = $_POST["message"];
        $message = str_replace("\n", "", $message);
        $outbound = $_SESSION["outbound"];
        $outbound[] = array("target" => $target, "body" => $message, "type" => "message");
        $_SESSION["outbound"] = $outbound;
        echo convert_emoji($message);
        break;
    case "changephone":
        $_SESSION['phone'] = $_POST['phone'];
        break;
    case "sendMedia":
        $target = $_POST["target"];
        $message = $_POST["message"];
        $outbound = $_SESSION["outbound"];
        $outbound[] = array("target" => $target, "body" => $message, "type" => $_POST["type"]);
        $_SESSION["outbound"] = $outbound;
        break;
    case "pollMessages":
        $inbound = @$_SESSION["inbound"];
        $_SESSION["inbound"] = array();
        $profilepic = @$_SESSION["profilepic"];
        $ret = new JSONResponse();
        if($profilepic != null && $profilepic != "")
        {
            $ret->profilepic = $profilepic;
        }
        $_SESSION["profilepic"] = "";
        if(count($inbound) > 0)
        {
            foreach($inbound as $i => $l)
            {
                foreach($l as $key => $value)
                {
                	if($key == 'message')
                    	$ret->messages[] = $value;
                    if($key == 'time')
                    	$ret->times[] = $value;
                    if($key == 'from')
                    	$ret->phones[] = $value;
                    if($key == 'name')
                    	$ret->names[] = $value;
                }
            }
            
        }
        echo json_encode($ret);
        break;
}