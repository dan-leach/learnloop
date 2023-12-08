<?php

function sendSessionCreatedMessage($name, $title, $feedbackID, $interactions, $id, $pin, $email)
{

    $messageContent = addHeader();

    $messageContent .= "
        <p>Hello " . $name . ",<br><br>
        A interact session has been successfully created on <a href='https://learnloop.co.uk'>LearnLoop</a> for your session '" . $title . "'. Please keep this email for future reference.</p>
        <span style='font-size:2em'>Your session ID is <strong>" . $id . "</strong><br>
        Your session PIN is <strong>" . $pin . "</strong></span><br>
        Do not share your PIN or this email with attendees. <a href='https://learnloop.co.uk/interact/resetPIN/" . $id . "'>Reset your PIN</a>.<br>
        <br>
        Your session contains the following interactions:<br>
    ";

    foreach (json_decode($interactions) as $interaction) {
        if ($interaction->prompt) $messageContent .= "- '" . $interaction->prompt . "'<br>";
    }

    if ($feedbackID) $messageContent .= "Your attendees will be directed at the end of the session to the feedback form with ID: " . $feedbackID . "<br>";

    $messageContent .= "
        <br><a href='https://learnloop.co.uk/interact/edit/" . $id . "'>Edit your session</a>.
        <p style='font-size:1.5em'>How to launch your interact session</p>
        Go to <a href='https://learnloop.co.uk/interact/host/" . $id . "'>learnloop.co.uk/interact/host/" . $id . "</a> and enter your session PIN.
        <p style='font-size:1.5em'>How to direct attendees to the interact session</p>
        When you launch your session instructions will appear onscreen for attendees to follow. Alternatively you can:
        <ul>
            <li>share the direct link: <a href='https://learnloop.co.uk/" . $id . "'>learnloop.co.uk/" . $id . "</a></li>
            <li>ask them to go to <a href='https://learnloop.co.uk/interact'>learnloop.co.uk/interact</a> and enter the session ID</li>
            <li><a href='https://learnloop.co.uk/interact/instructions/" . $id . "'> show a page with instructions on how to join</a> including a QR code for your attendees to scan</li>
        </ul>
        <br>
    ";

    $messageContent .= addFooter(false);

    $subject = 'Interact session created: ' . html_entity_decode($title);

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendSessionCreatedMessage", 500);

    return true;
}

function sendSessionUpdatedMessage($name, $title, $feedbackID, $interactions, $id, $email)
{

    $messageContent = addHeader();

    $messageContent .= "
        <p>Hello " . $name . ",<br><br>
        Your interact session '" . $title . "' has been successfully updated on <a href='https://learnloop.co.uk'>LearnLoop</a>.
        </p>
        <span style='font-size:2em'>Your session ID is <strong>" . $id . "</strong></span><br>
        Refer to the email sent when you session was created for your PIN or <a href='https://learnloop.co.uk/interact/resetPIN/" . $id . "'>reset your PIN</a>.<br>
        <br>
        Your session contains the following interactions:<br>
    ";

    foreach (json_decode($interactions) as $interaction) {
        if ($interaction->prompt) $messageContent .= "- '" . $interaction->prompt . "'<br>";
    }

    if ($feedbackID) $messageContent .= "Your attendees will be directed at the end of the session to the feedback form with ID: " . $feedbackID . "<br>";

    $messageContent .= "
        <br><a href='https://learnloop.co.uk/interact/edit/" . $id . "'>Make further changes to your session</a>.
        <p style='font-size:1.5em'>How to launch your interact session</p>
        Go to <a href='https://learnloop.co.uk/interact/host/" . $id . "'>learnloop.co.uk/interact/host/" . $id . "</a> and enter your session PIN.
        <p style='font-size:1.5em'>How to direct attendees to the interact session</p>
        When you launch your session instructions will appear onscreen for attendees to follow. Alternatively you can:
        <ul>
            <li>share the direct link: <a href='https://learnloop.co.uk/" . $id . "'>learnloop.co.uk/" . $id . "</a></li>
            <li>ask them to go to <a href='https://learnloop.co.uk/interact'>learnloop.co.uk/interact</a> and enter the session ID</li>
            <li><a href='https://learnloop.co.uk/interact/instructions/" . $id . "'> show a page with instructions on how to join</a> including a QR code for your attendees to scan</li>
        </ul>
        <br>
    ";

    $messageContent .= addFooter(false);

    $subject = 'Interact session updated: ' . html_entity_decode($title);

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendSessionUpdatedMessage", 500);

    return true;
}

function sendResetPin($name, $title, $email, $pin)
{

    $messageContent = addHeader();

    $messageContent .= "
        <p>Hello " . $name . ",<br><br>
        The PIN for your interact session <strong>'" . $title . "'</strong> been reset.</p>
        <p style='font-size:2em'>You new PIN is: <strong>" . $pin . "</strong></p>
    ";

    $messageContent .= addFooter(false);

    $subject = 'PIN reset for ' . html_entity_decode($title);

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendResetPin", 500);

    return true;
}

function sendFoundSessions($foundDetails, $email)
{
    $name = $foundDetails[count($foundDetails) - 1]['name'];

    $messageContent = addHeader();

    $messageContent .= "
        <p>Hello " . $name . ",<br><br>
        Here are the details of your interact sessions on LearnLoop.</p>
        <p>Go to <a href='https://learnloop.co.uk/interact/host'>LearnLoop.co.uk/interact/host</a> and use the session ID and PIN to launch your session. A link is provided to reset the PIN if you don't have the original.</p>
    ";

    foreach ($foundDetails as $details) {
        $messageContent .= "
            <p style='font-size:1.2em'>" . $details['title'] . "<br>
            Session ID:  " . $details['id'] . " | <a href='https://learnloop.co.uk/interact/resetPIN/" . $details['id'] . "'>Reset PIN</a></p>
        ";
    }

    $messageContent .= "Can't find the session you're looking for? Might you have used a different email? You can also reply to this email if you need help.<br><br>";

    $messageContent .= addFooter(false);

    $subject = 'Interact session history on LearnLoop';

    if (!sendMail($messageContent, $subject, $email, $name)) send_error_response("sendMail failed at sendFoundSessions", 500);

    return true;
}
