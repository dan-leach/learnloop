<?php

require 'database.php';
require 'mail.php';

//create
function insertSession($data, $link)
{
    $res = new stdClass();
    $res->id = createUniqueID($link, 'interact');
    $res->pin = createPin();
    $pinHash = hashPin($res->pin);

    //sanitize and validate
    $errMsg = "";

    $name = htmlspecialchars($data->name);
    if (strlen($name) == 0) $errMsg .= "Name is blank. ";
    $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errMsg .= "Email is not valid. ";
    global $betaTesters;
    if (!in_array($email, $betaTesters)) send_error_response("The email provided has not been authorised for beta-testing", 401);
    $feedbackID = ($data->feedbackID) ? htmlspecialchars($data->feedbackID) : "";
    if ($feedbackID) {
        if (!dbFeedbackSessionExists($feedbackID, $link)) send_error_response("Feedback session ID not recognised", 400);
    }
    $title = htmlspecialchars($data->title);
    if (strlen($title) == 0) $errMsg .= "Title is blank. ";
    if (sizeof($data->interactions) < 1) {
        $errMsg .= "No interactions defined. ";
    } else {
        $interactions = json_encode($data->interactions);
    }

    if (strlen($errMsg) > 0) send_error_response($errMsg, 400);

    if (!dbInsertSession($res->id, $name, $email, $title, $feedbackID, $interactions, $pinHash, $link)) send_error_response("dbInsertSession failed for an unknown reason", 500);

    if ($email) sendSessionCreatedMessage($name, $title, $feedbackID, $interactions, $res->id, $res->pin, $email);

    return $res;
}

//update
function resetPin($id, $data, $link)
{ //resets the pin for $id
    $details = dbSelectDetails($id, $link);
    $email = json_decode($data);
    if ($email != $details['email']) send_error_response("The email you provided does not match the facilitator email for '" . htmlspecialchars_decode($details['title']) . "'.", 400);
    $pin = createPin();
    $pinHash = hashPin($pin);
    if (!dbUpdatePinHash($id, $pinHash, $link)) send_error_response("dbUpdatePinHash failed for an unknown reason", 500);
    sendResetPin($details['name'], $details['title'], $details['email'], $pin);
    return "A new PIN has been emailed to you";
}
function updateSession($id, $pin, $data, $link)
{
    $res = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $res['pinHash'])) send_error_response("Invalid pin", 401);

    //sanitize and validate
    $errMsg = "";

    $name = htmlspecialchars($data->name);
    if (strlen($name) == 0) $errMsg .= "Name is blank. ";
    $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
    $title = htmlspecialchars($data->title);
    $feedbackID = htmlspecialchars($data->feedbackID);
    if ($feedbackID) {
        if (!dbFeedbackSessionExists($feedbackID, $link)) send_error_response("Feedback session ID not recognised", 400);
    }
    if (strlen($title) == 0) $errMsg .= "Title is blank. ";
    if (sizeof($data->interactions) < 1) {
        $errMsg .= "No interactions defined. ";
    } else {
        $interactions = json_encode($data->interactions);
    }

    if (strlen($errMsg) > 0) send_error_response($errMsg, 400);

    if (!dbUpdateSession($id, $name, $email, $title, $feedbackID, $interactions, $link)) send_error_response("dbUpdateSession failed for an unknown reason", 500);

    if (!dbDeleteSubmissions($id, $link)) send_error_response("dbDeleteSubmissions failed for an unknown reason", 500);

    if ($email) sendSessionUpdatedMessage($name, $title, $feedbackID, $interactions, $id, $email);

    return true;
}

//join
function fetchDetails($id, $link)
{
    $res = dbSelectDetails($id, $link);
    unset($res['email']);
    unset($res['pinHash']);
    $res['name'] = htmlspecialchars_decode($res['name']);
    $res['title'] = htmlspecialchars_decode($res['title']);
    $res['interactions'] = json_decode($res['interactions']);
    return $res;
}
function fetchFacilitatorIndex($id, $link)
{
    $res = dbSelectFacilitatorIndex($id, $link);
    return $res['facilitatorIndex'];
}
function insertSubmission($id, $data, $link)
{

    //sanitize and validate
    $interactionIndex = $data->interactionIndex;
    if (!filter_var($interactionIndex, FILTER_VALIDATE_INT)) send_error_response("interactionIndex must be of type [integer]", 400);
    $response = htmlspecialchars($data->response);
    if (strlen($response) == 0) send_error_response("Response cannot be blank", 400);

    if (!dbInsertSubmissiion($id, $interactionIndex, $response, $link)) send_error_response("response failed for an unknown reason", 500);

    return true;
}


//host
function fetchDetailsHost($id, $pin, $link)
{
    $res = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $res['pinHash'])) send_error_response("Invalid pin", 401);
    unset($res['pinHash']);
    $res['name'] = htmlspecialchars_decode($res['name']);
    $res['title'] = htmlspecialchars_decode($res['title']);
    $res['interactions'] = json_decode($res['interactions']);
    return $res;
}
function fetchNewSubmissions($id, $pin, $data, $link)
{
    $sessionDetails = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $sessionDetails['pinHash'])) send_error_response("Invalid pin", 401);
    $res = dbSelectNewSubmissions($id, $data->interactionIndex, $data->lastSubmissionId, $link);
    return $res;
}
function updateFacilitatorIndex($id, $pin, $data, $link)
{
    $sessionDetails = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $sessionDetails['pinHash'])) send_error_response("Invalid pin", 401);
    if (dbUpdateFacilitatorIndex($id, $data, $link)) return true;
    send_error_response("dbUpdateFacilitatorIndex failed for an unknown reason", 500);
}
function fetchSubmissionCount($id, $pin, $link)
{
    $sessionDetails = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $sessionDetails['pinHash'])) send_error_response("Invalid pin", 401);
    return (dbCountSubmissions($id, $link));
}
function deleteSubmissions($id, $pin, $link)
{
    $sessionDetails = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $sessionDetails['pinHash'])) send_error_response("Invalid pin", 401);
    if (dbDeleteSubmissions($id, $link)) return true;
    send_error_response("deleteSubmissions failed for an unknown reason", 500);
}

//utilities
function findMySessions($data, $link)
{
    $email = filter_var(json_decode($data), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) send_error_response("Invalid email.", 401);
    $foundDetails = dbFetchDetailsByEmail($email, $link);
    if (count($foundDetails) > 0) {
        if (!sendFoundSessions($foundDetails, $email)) send_error_response("sendFoundSessions failed for an unknown reason", 500);
    }
    return "If any sessions were found matching your email the details have been emailed to you.";
}
