<?php
// If you are using Composer (recommended)
require 'vendor/autoload.php';
require 'sc.php';


$from = new SendGrid\Email("郵便配達通知", "biff@uedasoft.com");
$subject = "Sending with SendGrid is Fun";
$to = new SendGrid\Email("UEDA", "ueda@uedasoft.com");
$content = new SendGrid\Content("text/plain", "メールだびょん");
$mail = new SendGrid\Mail($from, $subject, $to, $content);

$apiKey = SENDGRID_API_KEY;
$sg = new \SendGrid($apiKey);

$response = $sg->client->mail()->send()->post($mail);
echo $response->statusCode();
print_r($response->headers());
echo $response->body();
