<?php

require 'database.php';
require 'mail.php';

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
    if (filter_var($int, FILTER_VALIDATE_INT)) send_error_response("interactionIndex must be of type [integer]", 400);
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
?>