<?php

//send email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendMail($messageContent, $subject, $email, $name){
    /* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
    $mail = new PHPMailer(TRUE);

    /* Set the mail sender. */
    $mail->setFrom('mail@learnloop.co.uk', 'LearnLoop');

    /* Add a recipient. */
    $mail->addAddress($email, html_entity_decode($name));

    /* Set the subject. */
    $mail->Subject = $subject;

    $mail->isHTML(TRUE);
    $mail->AddEmbeddedImage('shared/logo.png', 'logo');
    /* Set the mail message body. */
    $mail->Body = $messageContent;

    /* Finally send the mail. */
    $mail->send();

    return true;
}

function addHeader(){
    return "
        <html>
            <img src='cid:logo' alt='LearnLoop Logo' height='50'><br>
            <br>
        ";
}

function addFooter($includeInvite){
    $res = "
            Kind regards,<br>
            <strong><a href='https://learnloop.co.uk'>LearnLoop</a></strong><br>";
    if ($includeInvite) $res .= "<br><i>You can request feedback for your own sessions using LearnLoop. Visit <a href='https://learnloop.co.uk'>learnloop.co.uk</a> to get started!</i>";
    $res .= "
        </html>";
    return $res;
}

?>
