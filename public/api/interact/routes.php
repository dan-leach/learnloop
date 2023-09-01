<?php

require 'database.php';
require 'mail.php';

//create
function insertSession($data, $link){
    $res = new stdClass();
    $res->id = createUniqueID($link);
    $res->pin = createPin();
    $pinHash = hashPin($res->pin);
    
    //sanitize and validate
    $errMsg = "";

    $name = htmlspecialchars($data->name);
    if (strlen($name) == 0) $errMsg .= "Name is blank. ";
    $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
    if (!$isSubsession && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errMsg .= "Email is not valid. ";
    $title = htmlspecialchars($data->title);
    if (strlen($title) == 0) $errMsg .= "Title is blank. ";
    if (sizeof($data->interactions) < 1) {
        $errMsg .= "No interactions defined. ";
    } else {
        $interactions = json_encode($data->interactions);
    }

    if (strlen($errMsg) > 0) send_error_response($errMsg, 400);
       
    if (!dbInsertSession($res->id, $name, $email, $title, $interactions, $pinHash, $link)) send_error_response("dbInsertSession failed for an unknown reason", 500);

    //if ($email) sendSessionCreatedMessage($isSubsession, $subsessionTitles, $date, $name, $title, $parentSessionName, $parentSessionTitle, $notifications, $tags, $certificate, $attendance, $questions, $res->id, $res->pin, $email);

    return $res;
}

//join
function fetchDetails($id, $link){
    $res = dbSelectDetails($id, $link);
    unset($res['email']);
    unset($res['pinHash']);
    $res['name'] = htmlspecialchars_decode($res['name']);
    $res['title'] = htmlspecialchars_decode($res['title']);
    $res['interactions'] = json_decode($res['interactions']);
    return $res;
}
function fetchFacilitatorIndex($id, $link){
    $res = dbSelectFacilitatorIndex($id, $link);
    return $res['facilitatorIndex'];
}
function insertSubmission($id, $data, $link){
    
    //sanitize and validate
    $interactionIndex = $data->interactionIndex;
    if (filter_var($interactionIndex, FILTER_VALIDATE_INT)) send_error_response("interactionIndex must be of type [integer]", 400);
    $response = htmlspecialchars($data->response);
    if (strlen($response) == 0) send_error_response("Response cannot be blank", 400);
        
    if (!dbInsertSubmissiion($id, $interactionIndex, $response, $link)) send_error_response("response failed for an unknown reason", 500);

    return true;
}


//host
function fetchDetailsHost($id, $pin, $link){
    $res = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $res['pinHash'])) send_error_response("Invalid pin", 401);
    unset($res['email']);
    unset($res['pinHash']);
    $res['name'] = htmlspecialchars_decode($res['name']);
    $res['title'] = htmlspecialchars_decode($res['title']);
    $res['interactions'] = json_decode($res['interactions']);
    return $res;
}
function fetchNewSubmissions($id, $pin, $data, $link){
    $sessionDetails = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $sessionDetails['pinHash'])) send_error_response("Invalid pin", 401);
    $res = dbSelectNewSubmissions($id, $data->interactionIndex, $data->lastSubmissionId, $link);    
    return $res;
}
function updateFacilitatorIndex($id, $pin, $data, $link){
    $sessionDetails = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $sessionDetails['pinHash'])) send_error_response("Invalid pin", 401);
    if (dbUpdateFacilitatorIndex($id, $data, $link)) return true;
    send_error_response("dbUpdateFacilitatorIndex failed for an unknown reason", 500);
}
function fetchSubmissionCount($id, $pin, $link){
    $sessionDetails = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $sessionDetails['pinHash'])) send_error_response("Invalid pin", 401);
    return (dbCountSubmissions($id, $link));
}
function deleteSubmissions($id, $pin, $link){
    $sessionDetails = dbSelectDetails($id, $link);
    if (!pinIsValid($pin, $sessionDetails['pinHash'])) send_error_response("Invalid pin", 401);
    if (dbDeleteSubmissions($id, $link)) return true;
    send_error_response("deleteSubmissions failed for an unknown reason", 500);
}
?>