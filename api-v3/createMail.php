<?php

//send email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function createMail($name, $date, $title, $email, $mailType, $notifications, $certificate, $id, $pin, $subsessions, $seriesName, $seriesTitle){

    $d = date_create($date);
    $date = date_format($d, 'd/m/Y');

    $messageContent = "
        <html>
            <img src='cid:logo' alt='LearnLoop Logo' height='50'><br>
            <br>
    ";

    if ($mailType == "single") {
        $messageContent .= "
            <h2>Hello " . $name . ", your session was created successfully!</h2>
            LearnLoop is ready to receive feedback from attendees of your session <strong>'" . $title . "'</strong> delivered on <strong>" . $date . "</strong>.
        ";
    }

    if ($mailType == "series") {
        $messageContent .= "
            <h2>Hello " . $name . ", your session series was created successfully!</h2>
            LearnLoop is ready to receive feedback from attendees of your session series <strong>'" . $title . "'</strong> delivered on <strong>" . $date . "</strong>.
        ";
    }


    if ($mailType == "subsession") {
        $messageContent .= "
            <h2>Hello " . $name . ", your session was created successfully!</h2>
            LearnLoop is ready to receive feedback from attendees of your session <strong>'" . $title . "'</strong> delivered on <strong>" . $date . "</strong>. This feedback request was created by " . $seriesName . " for the session series '" . $seriesTitle . "'.
        ";
    }

    $messageContent .= ($notifications) ? "Email notification of feedback submissions is <strong>enabled</strong>. <a href='https://learnloop.co.uk/?notifications=false&id=" . $id . "'>Click here to disable notifications</a>. " : "Email notification of feedback submissions is <strong>disabled</strong>. <a href='https://learnloop.co.uk/?notifications=true&id=" . $id . "'>Click here to enable notifications</a>. ";

    if ($mailType != "subsession"){
        $certificateIs = ($certificate) ? "enabled" : "disabled";
        $messageContent .= "Certificate for attendees after completing feedback is <strong>" . $certificateIs . "</strong>. ";
    }

    if ($mailType == "series") {
        $messageContent .= "Feedback will be collected on the sessions ";
            $i = 0;
            foreach ($subsessions as $sub) {
                $messageContent .= "'" . $sub->title . "'";
                $i++;
                if ($i == count($subsessions)) {
                    $messageContent .= ".";
                } elseif ($i == count($subsessions)-1) {
                    $messageContent .= " and ";
                } else {
                    $messageContent .= ", ";
                }
            }
    }

    $messageContent .= "
        <h3>View your feedback</h3>
        Go to <a href='https://learnloop.co.uk/?view=".$id."'>https://learnloop.co.uk/?view=".$id."</a> and enter your PIN: <strong>" . $pin . "</strong> to retrieve submitted feedback (do not share your PIN or this email with attendees).<br>
        <h3>How to direct attendees to the feedback form</h3>
    ";

    if ($mailType == "subsession") {
        $messageContent .= "
            The organiser of this session series will share the feedback link for the whole series with attendees. If needed you can use the details below which relate to just your session:<br>
        ";
    }

    $messageContent .= "
        You can share this link: <a href='https://learnloop.co.uk/?".$id."'>https://learnloop.co.uk/?".$id."</a><br>
        Or, ask them to go to <a href='https://learnloop.co.uk'>https://learnloop.co.uk</a> and enter the session ID: <strong>". $id . "</strong><br>
        Or, <a href='https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=https://learnloop.co.uk/?".$id."'>click here</a> to generate a QR code which you can save and insert into your presentation.<br>
        <br><br>
        Kind regards,<br>
        <strong>LearnLoop</strong><br>
    ";
    
    $messageContent .= ($mailType == "subsession") ? "<i>You can request feedback for your own sessions too using the LearnLoop. Visit <a href='https://learnloop.co.uk'>learnloop.co.uk</a> to get started!</i>" : "<a href='https://learnloop.co.uk'>learnloop.co.uk</a>";
    
    $messageContent .= "</html>";


    /* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
    $mail = new PHPMailer(TRUE);

    /* Set the mail sender. */
    $mail->setFrom('mail@learnloop.co.uk', 'LearnLoop');

    /* Add a recipient. */
    $mail->addAddress($email, html_entity_decode($name));

    /* Set the subject. */
    $mail->Subject = 'Session created: ' . html_entity_decode($title);

    $mail->isHTML(TRUE);
    $mail->AddEmbeddedImage('../logo.png', 'logo');
    /* Set the mail message body. */
    $mail->Body = $messageContent;

    /* Finally send the mail. */
    $mail->send();
}

function notificationMail($name, $date, $title, $email, $id, $mailType, $seriesName, $seriesTitle){

    $d = date_create($date);
    $date = date_format($d, 'd/m/Y');

    $messageContent = "
        <html>
            <img src='cid:logo' alt='LearnLoop Logo' height='50'><br>
            <br>
            <h2>Hello " . $name . ", feedback for your session has been submitted!</h2>
            An attendee has submitted feedback for your session <strong>'" . $title . "'</strong> delivered on <strong>" . $date . "</strong>.
        ";

    if ($mailType == "subsession") {
        $messageContent .= "
            This feedback request was created by " . $seriesName . " for the session series '" . $seriesTitle . "'.
        ";
    }
        
    $messageContent .= "
            <h3>View your feedback</h3>
            Go to <a href='https://learnloop.co.uk/?view=" . $id . "'>https://learnloop.co.uk/?view=" . $id . "</a> and enter your PIN (refer to the email you received when the feedback request was created to find your PIN).<br>
            <h3>Turn off notifications</h3>
            If you don't want to receive notifications when feedback is submitted you can disable this.
            <br><a href='https://learnloop.co.uk/?notifications=false&id=" . $id . "'>Click here to disable notifications.</a>
            <br><br>
            Kind regards,<br>
            <strong>LearnLoop</strong><br>
            <a href='https://learnloop.co.uk'>learnloop.co.uk</a>
        </html>";

    /* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
    $mail = new PHPMailer(TRUE);

    /* Set the mail sender. */
    $mail->setFrom('mail@learnloop.co.uk', 'LearnLoop');

    /* Add a recipient. */
    $mail->addAddress($email, html_entity_decode($name));

    /* Set the subject. */
    $mail->Subject = 'Feedback notification: ' . html_entity_decode($title);

    $mail->isHTML(TRUE);
    $mail->AddEmbeddedImage('../logo.png', 'logo');
    /* Set the mail message body. */
    $mail->Body = $messageContent;

    /* Finally send the mail. */
    $mail->send();
}

function pinMail($name, $date, $title, $email, $id, $pin){

    $d = date_create($date);
    $date = date_format($d, 'd/m/Y');

    $messageContent = "
        <html>
            <img src='cid:logo' alt='LearnLoop Logo' height='50'><br>
            <br>
            <h2>Hello " . $name . ", your new PIN is <strong>" . $pin . "</strong></h2>
            A PIN reset was requested for your session <strong>'" . $title . "'</strong> delivered on <strong>" . $date . "</strong>.
            <h3>View your feedback</h3>
            Go to <a href='https://learnloop.co.uk/?view=".$id."'>https://learnloop.co.uk/?view=".$id."</a> and enter your PIN: <strong>" . $pin . "</strong> to retrieve submitted feedback.
            <br><br>
            Kind regards,<br>
            <strong>LearnLoop</strong><br>
            <a href='https://learnloop.co.uk'>learnloop.co.uk</a>
        </html>";

    /* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
    $mail = new PHPMailer(TRUE);

    /* Set the mail sender. */
    $mail->setFrom('mail@learnloop.co.uk', 'LearnLoop');

    /* Add a recipient. */
    $mail->addAddress($email, html_entity_decode($name));

    /* Set the subject. */
    $mail->Subject = 'PIN reset for session: ' . html_entity_decode($title);

    $mail->isHTML(TRUE);
    $mail->AddEmbeddedImage('../logo.png', 'logo');
    /* Set the mail message body. */
    $mail->Body = $messageContent;

    /* Finally send the mail. */
    $mail->send();
}

function notificationPreferencesMail($name, $date, $title, $email, $id, $notifications){

    $d = date_create($date);
    $date = date_format($d, 'd/m/Y');

    $notificationsAre = ($notifications) ? "enabled" : "disabled";
    $notificationsTurned = ($notifications) ? "on" : "off";
    $youCan = ($notifications) ? "disable" : "re-enable";
    $toggleLink = ($notifications) ? "https://learnloop.co.uk/?notifications=false&id=".$id : "https://learnloop.co.uk/?notifications=true&id=".$id;

    $messageContent = "
        <html>
            <img src='cid:logo' alt='LearnLoop Logo' height='50'><br>
            <br>
            <h2>Hello " . $name . ", feedback notifications are now " . $notificationsAre . ".</h2>
            As requested feedback submission notifications are now turned " . $notificationsTurned . " for your session <strong>'" . $title . "'</strong> delivered on <strong>" . $date . "</strong>.<br>
            <a href='" . $toggleLink . "'>Click here if you want to " . $youCan . " notifications instead.</a>
            <br><br>
            Kind regards,<br>
            <strong>LearnLoop</strong><br>
            <a href='https://learnloop.co.uk'>learnloop.co.uk</a>
        </html>";

    /* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
    $mail = new PHPMailer(TRUE);

    /* Set the mail sender. */
    $mail->setFrom('mail@learnloop.co.uk', 'LearnLoop');

    /* Add a recipient. */
    $mail->addAddress($email, html_entity_decode($name));

    /* Set the subject. */
    $mail->Subject = 'Feedback notifications ' . $notificationsAre . ' for session: ' . html_entity_decode($title);

    $mail->isHTML(TRUE);
    $mail->AddEmbeddedImage('../logo.png', 'logo');
    /* Set the mail message body. */
    $mail->Body = $messageContent;

    /* Finally send the mail. */
    $mail->send();
}

?>