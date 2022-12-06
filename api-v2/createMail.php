<?php

//send email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$messageContent = "
    <html>
        <img src='cid:logo' alt='LearnLoop Logo' height='50'>
        <h1>Your session was created successfully</h1>
        Hello " . $fName . ",<br>
        <br>
        LearnLoop is ready to receive feedback from attendees of your session '" . $sName . "' that you delivered on " . $sDate . ". Submitted feedback will be sent to this email address.<br>
        <br>
        You can share this link with attendees:<br>
        <a href='https://learnloop.co.uk?".$uuid."'>https://learnloop.co.uk?".$uuid."</a><br>
        <br>
        Or ask them to go to <a href='https://learnloop.co.uk'>https://learnloop.co.uk</a> and enter the session ID: <strong>". $uuid . "</strong><br>
        <br>
        Or <a href='https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=https://learnloop.co.uk?".$uuid."'>click here</a> to generate a QR code which you can save and insert into your presentation.<br>
        <br>
        Kind regards,<br>
        LearnLoop<br>
        <a href='learnloop.co.uk'>learnloop.co.uk</a>
    </html>";

/* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
$mail = new PHPMailer(TRUE);

/* Set the mail sender. */
$mail->setFrom('mail@learnloop.co.uk', 'LearnLoop');

/* Add a recipient. */
$mail->addAddress($fEmail, $fName);

/* Set the subject. */
$mail->Subject = 'Session created: ' . $sName;

$mail->isHTML(TRUE);
$mail->AddEmbeddedImage('../logo.png', 'logo');
/* Set the mail message body. */
$mail->Body = $messageContent;

/* Finally send the mail. */
$mail->send();

?>