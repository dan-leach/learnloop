<?php

require 'database.php';
require 'utilities.php';
require 'mail.php';

//session creation
function insertSession($data, $link){ //inserts a new live session into the db, sends mail and returns a session id and pin
    $res = new stdClass();
    $res->id = createUniqueID($link, 'live');
    $res->pin = createPin();
    $pinHash = hashPin($res->pin);
    
    //sanitize and validate
    $errMsg = "";

    $name = htmlspecialchars($data->name);
    if (strlen($name) == 0) $errMsg .= "Name is blank. ";
    $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errMsg .= "Email is not valid. ";
    $title = htmlspecialchars($data->title);
    if (strlen($title) == 0) $errMsg .= "Title is blank. ";

    $questionIDs = array(); //stored later as json array in live session details table to link to questions
    $questionTitles = array(); //used by sendLiveSessionCreatedMessage to list questions
    if (isset($data->questions)) {
        foreach ($data->questions as $question) {
            $questionRes = insertLiveQuestion($question, $res->id, $link);
            array_push($questionIDs, $questionRes->id);
            array_push($questionTitles, $question->title);
        }
    } else {
        $errMsg .= "No questions defined. ";
    }

    if (strlen($errMsg) > 0) send_error_response($errMsg, 400);
    
    $questions = json_encode($questionIDs);
    
    if (!dbInsertLiveSession($res->id, $name, $email, $title, $questions, $pinHash, $link)) send_error_response("dbInsertLiveSession failed for an unknown reason", 500);

    if ($email) sendLiveSessionCreatedMessage($questionTitles, $name, $title, $res->id, $res->pin, $email);

    return $res;
}
function insertQuestion($question, $sessionID, $link){
    $res = new stdClass();
    $res->id = createUniqueID($link, 'question');
    
    //sanitize and validate
    $title = htmlspecialchars($question->title);
    if (strlen($title) == 0) send_error_response("Question title cannot be blank", 400);
    $type = htmlspecialchars($question->type);
    if (strlen($type) == 0) send_error_response("Question type cannot be blank", 400);
    $options = json_encode($question->options);
    if (strlen($options) == 0) send_error_response("Question options cannot be blank", 400);
    
    if (!dbInsertLiveQuestion($res->id, $sessionID, $title, $type, $options, $link)) send_error_response("dbInsertLiveQuestion failed for an unknown reason", 500);

    return $res;
}

//join
function fetchDetails($id, $link){
    $res = dbSelectLiveDetails($id, $link);
    unset($res['email']);
    unset($res['pinHash']);
    $res['name'] = htmlspecialchars_decode($res['name']);
    $res['title'] = htmlspecialchars_decode($res['title']);
    
    $questionIDs = json_decode($res['questions']);
    $res['questions'] = array();
    foreach ($questionIDs as $questionID) {
        $questionDetails = dbSelectLiveQuestionDetails($questionID, $link);
        array_push($res['questions'], $questionDetails);
    }
    
    return $res;
}
function insertSubmission($questionID, $submission, $link){
    
    //sanitize and validate
    $questionID = htmlspecialchars($questionID);
    if (strlen($questionID) == 0) send_error_response("questionID cannot be blank", 400);
    $submission = htmlspecialchars($submission);
    if (strlen($submission) == 0) send_error_response("Submission cannot be blank", 400);
        
    if (!dbInsertLiveSubmission($questionID, $submission, $link)) send_error_response("dbInsertLiveSubmission failed for an unknown reason", 500);

    if (!dbUpdateLiveQuestionLastSubmissionChecksum($questionID, crc32($submission), $link)) send_error_response("dbUpdateLiveQuestionLastSubmissionChecksum failed for an unknown reason", 500);

    return true;
}

//view
function fetchViewDetails($id, $pin, $link){
    $res = dbSelectLiveDetails($id, $link);
    unset($res['email']);
    if (!pinIsValid($pin, $res['pinHash'])) send_error_response("Invalid pin", 401);
    unset($res['pinHash']);
    $res['name'] = htmlspecialchars_decode($res['name']);
    $res['title'] = htmlspecialchars_decode($res['title']);
    
    $questionIDs = json_decode($res['questions']);
    $res['questions'] = array();
    foreach ($questionIDs as $questionID) {
        $questionDetails = dbSelectLiveQuestionDetails($questionID, $link);
        array_push($res['questions'], $questionDetails);
    }

    //get the submissions for the first question
    $res['questions'][0]['responses'] = dbSelectLiveQuestionSubmissions($questionIDs[0], $link);
    
    return $res;
}
?>