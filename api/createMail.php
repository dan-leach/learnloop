<?php

//send email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$messageContent = "
    <html>
        <img src='cid:logo' alt='Feedback Tool Logo' height='50'>
        <h1>New session created on feedback tool.</h1>
        Hello " . $fName . ",<br>
        <br>
        Feedback tool is ready to receive feedback from attendees of your session '" . $sName . "' that you delivered on " . $sDate . ". Submitted feedback will be sent to this email address.<br>
        <br>
        You can share this link with attendees:<br>
        <a href='https://feedback.danleach.uk/?".$uuid."'>https://feedback.danleach.uk/?".$uuid."</a><br>
        <br>
        Or ask them to go to <a href='https://feedback.danleach.uk'>https://feedback.danleach.uk</a> and ask them to enter the session ID: <strong>". $uuid . "</strong><br>
        <br>
        Or <a href='https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=https://feedback.danleach.uk/?".$uuid."'>click here</a> to generate a QR code which you can save and insert into your presentation.<br>
        <br>
        Kind regards,<br>
        Feedback Tool<br>
        <a href='feedback.danleach.uk'>feedback.danleach.uk</a>
    </html>";

/* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
$mail = new PHPMailer(TRUE);

/* Set the mail sender. */
$mail->setFrom('feedback@danleach.uk', 'Feedback Tool');

/* Add a recipient. */
$mail->addAddress($fEmail, $fName);

/* Set the subject. */
$mail->Subject = 'Feedback Tool Session Created';

$mail->isHTML(TRUE);
$mail->AddEmbeddedImage('../logo.png', 'logo');
/* Set the mail message body. */
$mail->Body = $messageContent;

/* Finally send the mail. */
$mail->send();

?>