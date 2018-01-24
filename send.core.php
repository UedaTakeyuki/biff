<?php
require_once("vendor/autoload.php");   
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$log = new Logger('send.core');
$log->pushHandler(new StreamHandler('biff.log', Logger::DEBUG));
$log->debug('** Start **', ['file' => __FILE__, 'line' => __LINE__]);
$log->debug('to = '.$_POST['to'], ['file' => __FILE__, 'line' => __LINE__]);

if ($_POST['to'] == "zenin"){
	foreach ($addresses as $key => $value){
		send_notification($value->name, $value->address);
		$log->debug('** message sent **', ['file' => __FILE__, 'line' => __LINE__]);
	}
} else {
	if (array_key_exists($_POST['to'], $addresses)){
		$name    = $addresses[$_POST['to']]->name;
		$address = $addresses[$_POST['to']]->address;
	}
	send_notification($name, $address);
	$log->debug('** message sent **', ['file' => __FILE__, 'line' => __LINE__]);
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
