<?php

function sendSessionCreatedMessage($isSubsession, $subsessionTitles, $date, $name, $title, $seriesName, $seriesTitle, $notifications, $certificate, $attendance, $questions, $id, $pin, $email)
{
    global $rootURL;

    $messageContent = addHeader();

    $messageContent .= "
            <p>Hello " . $name . ",<br><br>
            A feedback request has been successfully created on <a href='" . $rootURL . "'>LearnLoop</a> for your session '" . $title . "' delivered on " . $date . ".
        ";

    if ($isSubsession) $messageContent .= "<br>This feedback request was created by " . $seriesName . " for the session series '" . $seriesTitle . "'.";

    $messageContent .= "
        </p><p>Please keep this email for future reference.</p>
        <span style='font-size:2em'>Your session ID is <strong>" . $id . "</strong><br>
        Your session PIN is <strong>" . $pin . "</strong></span><br>
        Do not share your PIN or this email with attendees. <a href='" . $rootURL . "/feedback/resetPIN/" . $id . "'>Reset your PIN</a>.<br>
    ";

    if ($subsessionTitles) {
        $messageContent .= "Feedback will be collected on the sessions ";
        $i = 0;
        foreach ($subsessionTitles as $subTitle) {
            $messageContent .= "'" . $subTitle . "'";
            $i++;
            if ($i == count($subsessionTitles)) {
                $messageContent .= ".<br>";
            } elseif ($i == count($subsessionTitles) - 1) {
                $messageContent .= " and ";
            } else {
                $messageContent .= ", ";
            }
        }
    }

    if (count(json_decode($questions))) {
        $messageContent .= "The following additional questions will be asked:<br>";
        foreach (json_decode($questions) as $question) $messageContent .= "- '" . $question->title . "'<br>";
        $messageContent .= "<br>";
    }

    $messageContent .= "<a href='" . $rootURL . "/feedback/edit/" . $id . "'>Edit your session</a>. This option is only available <strong>before</strong> feedback has been submitted.
        <p style='font-size:1.5em'>How to direct attendees to the feedback form</p>
    ";

    if ($isSubsession) {
        $messageContent .= "
            <p>The organiser of this session series will share the feedback link for the whole series with attendees.<p>
        ";
    } else {
        $messageContent .= "
            You can share the direct link: <a href='" . $rootURL . "/" . $id . "'>" . str_replace("https://", "", $rootURL) . "/" . $id . "</a><br>
            Or, ask them to go to <a href='" . $rootURL . "'>" . str_replace("https://", "", $rootURL) . "</a> and enter the session ID.<br>
            Or, <a href='" . $rootURL . "/feedback/instructions/" . $id . "'>show a page with instructions on how to reach the feedback form</a> including a QR code for your attendees to scan.<br>
        ";
        if (!$isSubsession && $certificate) $messageContent .= "<br>Don't forget to let your attendees know that they'll be able to download a certificate of attendance after completing feedback.";
    }

    $messageContent .= "
        <p style='font-size:1.5em'>View your feedback</p>
        <p>Go to <a href='" . $rootURL . "/feedback/view/" . $id . "'>" . str_replace("https://", "", $rootURL) . "/feedback/view/" . $id . "</a> and enter your PIN to retrieve submitted feedback.<br>";
    $messageContent .= ($notifications) ? "Email notification of feedback submissions is <strong>enabled</strong>. " : "Email notification of feedback submissions is <strong>disabled</strong>. ";
    $messageContent .= "<a href='" . $rootURL . "/feedback/notifications/" . $id . "'>Update your notification preferences</a>.<br>
    ";
    if (!$isSubsession && $attendance) $messageContent .= "The attendance register is <strong>enabled</strong>. <a href='" . $rootURL . "/feedback/attendance/" . $id . "'>View attendance register</a>.<br>";
    $messageContent .= "<br>";

    $messageContent .= addFooter($isSubsession);

    $subject = 'Feedback request created: ' . html_entity_decode($title);

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendSessionCreatedMessage", 500);

    return true;
}

function sendSessionUpdatedMessage($isSubsession, $subsessionTitles, $date, $name, $title, $seriesName, $seriesTitle, $notifications, $certificate, $attendance, $questions, $id, $email)
{
    global $rootURL;

    $messageContent = addHeader();

    $messageContent .= "
            <p>Hello " . $name . ",<br><br>
            Your feedback request has been successfully updated on <a href='" . $rootURL . "'>LearnLoop</a> for your session '" . $title . "' delivered on " . $date . ".
        ";

    if ($isSubsession) $messageContent .= "<br>This feedback request was created by " . $seriesName . " for the session series '" . $seriesTitle . "'.";

    $messageContent .= "
        </p>
        <span style='font-size:2em'>Your session ID is <strong>" . $id . "</strong></span><br>
        Refer to the email sent when you session was created for your PIN or <a href='" . $rootURL . "/feedback/resetPIN/" . $id . "'>reset your PIN</a>.<br>
    ";

    if ($subsessionTitles) {
        $messageContent .= "Feedback will be collected on the sessions ";
        $i = 0;
        foreach ($subsessionTitles as $subTitle) {
            $messageContent .= "'" . $subTitle . "'";
            $i++;
            if ($i == count($subsessionTitles)) {
                $messageContent .= ".<br>";
            } elseif ($i == count($subsessionTitles) - 1) {
                $messageContent .= " and ";
            } else {
                $messageContent .= ", ";
            }
        }
    }

    if (count(json_decode($questions))) {
        $messageContent .= "The following additional questions will be asked:<br>";
        foreach (json_decode($questions) as $question) $messageContent .= "- '" . $question->title . "'<br>";
        $messageContent .= "<br>";
    }

    $messageContent .= "<a href='" . $rootURL . "/feedback/edit/" . $id . "'>Make further edits to your session</a>. This option is only available <strong>before</strong> feedback has been submitted.
        <p style='font-size:1.5em'>How to direct attendees to the feedback form</p>
    ";

    if ($isSubsession) {
        $messageContent .= "
            <p>The organiser of this session series will share the feedback link for the whole series with attendees.<p>
        ";
    } else {
        $messageContent .= "
            You can share the direct link: <a href='" . $rootURL . "/" . $id . "'>" . str_replace("https://", "", $rootURL) . "/" . $id . "</a><br>
            Or, ask them to go to <a href='" . $rootURL . "/feedback'>" . str_replace("https://", "", $rootURL) . "/feedback</a> and enter the session ID.<br>
            Or, <a href='" . $rootURL . "/feedback/instructions/" . $id . "'>show a page with instructions on how to reach the feedback form</a> including a QR code for your attendees to scan.<br>
        ";
        if (!$isSubsession && $certificate) $messageContent .= "<br>Don't forget to let your attendees know that they'll be able to download a certificate of attendance after completing feedback.";
    }

    $messageContent .= "
        <p style='font-size:1.5em'>View your feedback</p>
        <p>Go to <a href='" . $rootURL . "/feedback/view/" . $id . "'>" . str_replace("https://", "", $rootURL) . "/feedback/view/" . $id . "</a> and enter your PIN to retrieve submitted feedback.<br>";
    $messageContent .= ($notifications) ? "Email notification of feedback submissions is <strong>enabled</strong>. " : "Email notification of feedback submissions is <strong>disabled</strong>. ";
    $messageContent .= "<a href='" . $rootURL . "/feedback/notifications/" . $id . "'>Update your notification preferences</a>.<br>
    ";
    if (!$isSubsession && $attendance) $messageContent .= "The attendance register is <strong>enabled</strong>. <a href='" . $rootURL . "/feedback/attendance/" . $id . "'>View attendance register</a>.<br>";
    $messageContent .= "<br>";

    $messageContent .= addFooter($isSubsession);

    $subject = 'Feedback request updated: ' . html_entity_decode($title);

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendSessionUpdatedMessage", 500);

    return true;
}

function sendFeedbackNotification($isSubsession, $date, $name, $title, $seriesName, $seriesTitle, $id, $email)
{
    global $rootURL;
    
    $d = date_create($date);
    $date = date_format($d, 'd/m/Y');

    $messageContent = addHeader();

    $messageContent .= "
            <p>Hello " . $name . ",<br><br>
            An attendee has submitted feedback for your session <strong>'" . $title . "'</strong>.
        ";

    if ($isSubsession) $messageContent .= "<br>This feedback request was created by " . $seriesName . " for the session series '" . $seriesTitle . "'.";

    $messageContent .= "
        </p><p style='font-size:1.5em'>View your feedback</p>
        <p>Go to <a href='" . $rootURL . "/feedback/view/" . $id . "'>" . str_replace("https://", "", $rootURL) . "/feedback/view/" . $id . "</a> and enter your PIN (refer to session creation email, or <a href='" . $rootURL . "/feedback/resetPIN/" . $id . "'>reset your PIN</a>) to retrieve submitted feedback.<br>
        Please note, to avoid overloading your inbox, no further notifications will be sent for feedback submitted within the next 2 hours.</p>
        <p><a href='" . $rootURL . "/feedback/notifications/" . $id . "'>Update your notification preferences</a> if you don't want to receive these emails.</p>
    ";

    $messageContent .= addFooter(false);

    $subject = 'Feedback notification for ' . html_entity_decode($title);

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendFeedbackNotification", 500);

    return true;
}

function sendNotificationPreferenceStatus($id, $date, $name, $title, $email, $notifications)
{
    global $rootURL;

    $d = date_create($date);
    $date = date_format($d, 'd/m/Y');

    $messageContent = addHeader();

    $messageContent .= "
        <p>Hello " . $name . ",<br><br>
        Your notification preferences have been updated.</p>
    ";

    $messageContent .= ($notifications) ? "Email notification of feedback submissions is <strong>enabled</strong>. " : "Email notification of feedback submissions is <strong>disabled</strong>. ";
    $messageContent .= "<br><br><a href='" . $rootURL . "/feedback/notifications/" . $id . "'>Click here</a> to update your notification preferences.<br><br>";

    $messageContent .= addFooter(false);

    $subject = 'Notification preferences updated for ' . html_entity_decode($title);

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendNotificationPreferenceStatus", 500);

    return true;
}

function sendResetPin($name, $title, $email, $pin)
{

    $messageContent = addHeader();

    $messageContent .= "
        <p>Hello " . $name . ",<br><br>
        The PIN for your session <strong>'" . $title . "'</strong> been reset.</p>
        <p style='font-size:2em'>You new PIN is: <strong>" . $pin . "</strong></p>
    ";

    $messageContent .= addFooter(false);

    $subject = 'PIN reset for ' . html_entity_decode($title);

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendResetPin", 500);

    return true;
}

function sendFoundSessions($foundDetails, $email)
{
    global $rootURL;
    
    $name = $foundDetails[count($foundDetails) - 1]['name'];

    $messageContent = addHeader();

    $messageContent .= "
        <p>Hello " . $name . ",<br><br>
        Here are the details of your sessions on LearnLoop.</p>
        <p>Go to <a href='" . $rootURL . "'>" . str_replace("https://", "", $rootURL) . "</a> and use the session ID and PIN to view submitted feedback or the attendance register. A link is provided to reset the PIN if you don't have the original.</p>
    ";

    foreach ($foundDetails as $details) {
        $messageContent .= "
            <p style='font-size:1.2em'>" . $details['title'] . "<br>
            Date: " . formatDateHuman($details['date']) . " | Session ID:  " . $details['id'] . " | Status: " . ($details['closed'] ? "closed" : "open") . " | <a href='" . $rootURL . "/feedback/resetPIN/" . $details['id'] . "'>Reset PIN</a></p>
        ";
    }

    $messageContent .= "Can't find the session you're looking for? Might you have used a different email? You can also reply to this email if you need help.<br><br>";

    $messageContent .= addFooter(false);

    $subject = 'Session history on LearnLoop';

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendFoundSessions", 500);

    return true;
}

function sendSessionClosed($details, $isSubsession)
{

    global $adminEmail;

    $messageContent = addHeader();

    $messageContent .= "
        <p>Hello " . $details['name'] . ",<br><br>
        Your feedback request for <strong>'" . $details['title'] . "'</strong> has been closed and is no longer accepting submissions.</p>
    ";
    if ($isSubsession) {
        $messageContent .= "<p>This action was performed by the session series organiser.</p>";
    } else {
        $messageContent .= "<p>If you didn't request this please contact <a href='mailto:" . $adminEmail . "'>". $adminEmail . "</a> for support.</p>";
    }

    $messageContent .= addFooter(false);

    $subject = 'Feedback request closed ' . html_entity_decode($details['title']);

    if (!sendMail($messageContent, $subject, $details['email'], $details['name'])) send_error_response("sendMail failed at sendSessionClosed", 500);

    return true;
}
