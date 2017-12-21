<?php
// If you are using Composer (recommended)
require 'vendor/autoload.php';
require 'sc.php';
#require_once "sc2.php";

$name = "上田さん";
$address = ADDRESS_UEDA;
if ($_POST['to'] == "zenin"){
	foreach ($addresses as $key => $value){
		send_notification($value->name, $value->address);
	}
} else {
	if (array_key_exists($_POST['to'], $addresses)){
		$name    = $addresses[$_POST['to']]->name;
		$address = $addresses[$_POST['to']]->address;
	}
	send_notification($name, $address);
}

function send_notification($name, $address){
	$string = $name."\n";
	$string = $string.$_POST['now']."に、書類が届きました\n";
	$string = $string."http://titurel.uedasoft.com/biff/uploads/".$_POST['filename'];

	$from = new SendGrid\Email("配達通知", "biff@uedasoft.com");
	$subject = "書類がとどいています";
	$to = new SendGrid\Email($name, $address);
	$content = new SendGrid\Content("text/plain", $string);
	$mail = new SendGrid\Mail($from, $subject, $to, $content);

	$apiKey = SENDGRID_API_KEY;
	$sg = new \SendGrid($apiKey);

	$response = $sg->client->mail()->send()->post($mail);
	echo $response->statusCode();
	print_r($response->headers());
	echo $response->body();
}
