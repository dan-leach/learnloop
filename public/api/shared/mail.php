<?php

//send email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendMail($messageContent, $subject, $email, $name){
    global $rootURL;
    global $adminEmail;
    /* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
    $mail = new PHPMailer(TRUE);

    /* Set the mail sender. */
    $mail->setFrom($adminEmail, 'LearnLoop');

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
    global $devMode;
    global $rootURL;
    global $adminEmail;

    $res = "";

    if ($devMode) $res .= "This session uses the development version of LearnLoop and may include experimental features which might not be supported long-term. Please report any bugs or other feedback to <a href='mailto:" . $adminEmail . "'>" . $adminEmail . "</a>.<br><br>";

    $res .= "
            Kind regards,<br>
            <strong><a href='" . $rootURL . "'>LearnLoop</a></strong><br>";
    if ($includeInvite) $res .= "<br><i>You can request feedback for your own sessions using LearnLoop. Visit <a href='" . $rootURL . "'>" . str_replace("https://", "", $rootURL) . "</a> to get started!</i>";
    $res .= "
        </html>";
    return $res;
}

?>
