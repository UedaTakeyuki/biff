<?php
// If you are using Composer (recommended)
require 'vendor/autoload.php';
require 'sc.php';

$name = "上田さん";
$address = ADDRESS_UEDA;
switch ($_POST['to']){
	case "yamazaki":
	  $name = "山崎様";
	  $address = ADDRESS_YAMAZAKI;
	  break;
}
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
