<?php

require 'database.php';
require 'mail.php';

//session creation
function insertSession($data, $isSubsession, $parentSessionDate, $parentSessionName, $parentSessionTitle, $link){ //inserts a new session into the db, sends mail and returns a session id and pin
    $res = new stdClass();
    $res->id = createUniqueID($link, 'feedback');
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

    if ($isSubsession) {
        $date = $parentSessionDate;
        $notifications = true;
        $attendance = false;
        $certificate = false;
        $questions = '[]';
        
    } else {
        $date = htmlspecialchars($data->date);
        $notifications = filter_var($data->notifications, FILTER_VALIDATE_BOOLEAN);
        $attendance = filter_var($data->attendance, FILTER_VALIDATE_BOOLEAN);
        $certificate = filter_var($data->certificate, FILTER_VALIDATE_BOOLEAN);
        $questions = ($data->questions) ? json_encode($data->questions) : '[]';
    }

    if (!validateDate($date, 'Y-m-d')) {
        if(!validateDate($date, 'd/m/Y')){
            $errMsg .= "Date is not valid. ";
        } else {
            $d = substr($date, 0, 2);
            $m = substr($date, 3, 2);
            $y = substr($date, 6, 4);
            $date = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));;
        }
    }
        
    if (strlen($errMsg) > 0) send_error_response($errMsg, 400);

    $subsessionIDs = array(); //stored later as json array in session details table to link to subsessions
    $subsessionTitles = array(); //used by sendSessionCreatedMessage to list subsessions
    if (isset($data->subsessions)) {
        if ($isSubsession) send_error_response("session is being iterated as a subsession by insertSession but has subsessions itself", 500);
        foreach ($data->subsessions as $sub) {
            $subRes = insertSession($sub, true, $date, $name, $title, $link);
            array_push($subsessionIDs, $subRes->id);
            array_push($subsessionTitles, $sub->title);
        }
    }
    
    $subsessions = ($subsessionIDs) ? json_encode($subsessionIDs) : '[]';
    
    if (!dbInsertSession($res->id, $name, $email, $title, $date, $questions, $certificate, $subsessions, $isSubsession, $notifications, $attendance, $pinHash, $link)) send_error_response("dbInsertSession failed for an unknown reason", 500);
    
    $d = date_create($date);
    $date = date_format($d, 'd/m/Y');
    $res->date = $date;

    if ($email) sendSessionCreatedMessage($isSubsession, $subsessionTitles, $date, $name, $title, $parentSessionName, $parentSessionTitle, $notifications, $certificate, $attendance, $questions, $res->id, $res->pin, $email);

    return $res;
}

//session updates
function loadUpdateDetails($id, $pin, $isSubsession, $link){ //fetch the details before doing update
    $res = dbSelectDetails($id, $link);
    if (!$isSubsession && !pinIsValid($pin, $res['pinHash'])) send_error_response("Invalid pin", 401);
    $feedback = dbSelectFeedback($id, $link);
    if ($res['closed']) send_error_response("This feedback request has been closed.", 500);
    if ($feedback['score'][0] != "No feedback found.") send_error_response("Cannot edit session as feedback has already been submitted", 500);
    $res['name'] = htmlspecialchars_decode($res['name']);
    $res['title'] = htmlspecialchars_decode($res['title']);
    unset($res['lastSent']);
    unset($res['pinHash']);
    $res['questions'] = json_decode($res['questions']);
    if ($res['subsessions']){
        $subsessionIDs = json_decode($res['subsessions']);
        unset($res['subsessions']);
        $res['subsessions'] = array();
        foreach ($subsessionIDs as $sub) {
            $subsessionDetails = loadUpdateDetails($sub, null, true, $link);
            array_push($res['subsessions'], $subsessionDetails);
        }
    }
    return $res;
}
function updateDetails($id, $pin, $data, $isSubsession, $firstTimeEmailProvided, $parentSessionDate, $parentSessionName, $parentSessionTitle, $link){ //updates the details for $id
    $details = dbSelectDetails($id, $link);
    if (!$isSubsession && !pinIsValid($pin, $details['pinHash'])) send_error_response("Invalid pin", 401);
    if ($details['closed']) send_error_response("This feedback request has been closed.", 500);

    //sanitize and validate
    $errMsg = "";

    $name = htmlspecialchars($data->name);
    if (strlen($name) == 0) $errMsg .= "Name is blank. ";
    $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
    if (!$isSubsession && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errMsg .= "Email is not valid. ";
    $title = htmlspecialchars($data->title);
    if (strlen($title) == 0) $errMsg .= "Title is blank. ";

    if ($isSubsession) {
        $date = $parentSessionDate;
        $notifications = true;
        $attendance = false;
        $certificate = false;
        $questions = '[]';
        
    } else {
        $date = htmlspecialchars($data->date);
        $notifications = filter_var($data->notifications, FILTER_VALIDATE_BOOLEAN);
        $attendance = filter_var($data->attendance, FILTER_VALIDATE_BOOLEAN);
        $certificate = filter_var($data->certificate, FILTER_VALIDATE_BOOLEAN);
        $questions = ($data->questions) ? json_encode($data->questions) : '[]';
    }

    if (!validateDate($date, 'Y-m-d')) {
        if(!validateDate($date, 'd/m/Y')){
            $errMsg .= "Date is not valid. ";
        } else {
            $d = substr($date, 0, 2);
            $m = substr($date, 3, 2);
            $y = substr($date, 6, 4);
            $date = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));;
        }
    }
        
    if (strlen($errMsg) > 0) send_error_response($errMsg, 400);

    $subsessionIDs = array(); //stored later as json array in session details table to link to subsessions
    $subsessionTitles = array(); //used by sendSessionUpdatedMessage to list subsessions
    if ($data->subsessions) {
        if ($isSubsession) send_error_response("session is being iterated as a subsession by updateDetails but has subsessions itself", 500);
        foreach ($data->subsessions as $sub) {
            if (isset($sub->id)){
                $isNew = !str_contains($details['subsessions'], $sub->id);
            } else {
                $isNew = true;
            }
            if ($isNew) {
                $subRes = insertSession($sub, true, $date, $name, $title, $link);
                $sub->id = $subRes->id;
            } else {
                $subExistingDetails = dbSelectDetails($sub->id, $link);
                $subFirstTimeEmailProvided = ($subExistingDetails['email']) ? false : true;
                if ($subExistingDetails['name'] != $sub->name || $subExistingDetails['email'] != $sub->email || $subExistingDetails['title'] != $sub->title) updateDetails($sub->id, null, $sub, true, $subFirstTimeEmailProvided, $date, $name, $title, $link);
            }
            array_push($subsessionIDs, $sub->id);
            array_push($subsessionTitles, $sub->title);
        }

        foreach (json_decode($details['subsessions']) as $subID) {
            if (!in_array($subID, $subsessionIDs)) closeSession($subID, null, true, $link);
        }
    }
    $subsessions = ($subsessionIDs) ? json_encode($subsessionIDs) : '[]';
    if (!dbUpdateDetails($id, $name, $email, $title, $date, $questions, $certificate, $subsessions, $notifications, $attendance, $link)) send_error_response("dbUpdateDetails failed for an unknown reason", 500);
    $d = date_create($date);
    $date = date_format($d, 'd/m/Y');
    if ($firstTimeEmailProvided) {
        $pin = createPin();
        $pinHash = hashPin($pin);
        dbUpdatePinHash($id, $pinHash, $link);
        sendSessionCreatedMessage(true, null, $date, $name, $title, $parentSessionName, $parentSessionTitle, $notifications, $certificate, $attendance, $questions, $id, $pin, $email);
    } else {
        sendSessionUpdatedMessage($isSubsession, $subsessionTitles, $date, $name, $title, $parentSessionName, $parentSessionTitle, $notifications, $certificate, $attendance, $questions, $id, $email);
    }
    return "Session updated successfully.";
}
function setNotificationPreference($id, $pin, $data, $link){ //sets the notifications for $id to $data (true/false)
    $details = dbSelectFeedback($id, $link);
    if (!pinIsValid($pin, $details['pinHash'])) send_error_response("Invalid pin", 401);
    if ($details['closed']) send_error_response("This feedback request has been closed.", 500);
    if (!is_bool($data)) send_error_response("setNotificationPreference [data] parameter must be a boolean value (true/false)", 500);
    if (!dbSetNotificationPreference($id, $data, $link)) send_error_response("dbSetNotificationPreference failed for an unknown reason", 500);
    $details = dbSelectDetails($id, $link);
    sendNotificationPreferenceStatus($id, $details['date'], $details['name'], $details['title'], $details['email'], $data);
    $res = "Notifications have been set: ";
    $res .= ($data) ? "ON" : "OFF";
    return $res;
}
function resetPin($id, $data, $link){ //resets the pin for $id
    $details = dbSelectDetails($id, $link);
    if ($data != $details['email']) send_error_response("The email you provided does not match the facilitator email for '".$details['title']."'.", 500);
    if ($details['closed']) send_error_response("This feedback request has been closed.", 500);
    $pin = createPin();
    $pinHash = hashPin($pin);
    if (!dbUpdatePinHash($id, $pinHash, $link)) send_error_response("dbUpdatePinHash failed for an unknown reason", 500);
    sendResetPin($details['name'], $details['title'], $details['email'], $pin);
    return "A new PIN has been emailed to you";
}
function closeSession($id, $pin, $skipChecks, $link){ //marks the session and any subsessions as closed
    $details = dbSelectDetails($id, $link);
    if (!$skipChecks){
        if ($details['closed']) send_error_response("This feedback request has already been closed.", 400);
        if ($details['isSubsession']) send_error_response("This feedback request is part of a session series and can only be closed by the series organiser.", 401);
        if (!pinIsValid($pin, $details['pinHash'])) send_error_response("Invalid pin", 401);
    }
    if (!dbCloseSession($id, $link)) send_error_response("dbCloseSession failed for an unknown reason", 500);
    if ($details['subsessions']){
        $subsessionIDs = json_decode($details['subsessions']);
        foreach ($subsessionIDs as $subID) {
            $subDetails = dbSelectDetails($subID, $link);
            if (!dbCloseSession($subID, $link)) send_error_response("dbCloseSession failed for an unknown reason", 500);
            if ($subDetails['email']) sendSessionClosed($subDetails, true);
        }
    }
    sendSessionClosed($details, false);
    return "Session closed to feedback.";
}

//give feedback
function fetchDetails($id, $link){ //returns the session details for $id as json object
    $res = dbSelectDetails($id, $link);
    $res['questions'] = json_decode($res['questions']);
    unset($res['email']);
    unset($res['lastSent']);
    unset($res['pinHash']);
    $res['name'] = htmlspecialchars_decode($res['name']);
    $res['title'] = htmlspecialchars_decode($res['title']);
    $res['date'] = formatDateHuman($res['date']);
    if ($res['subsessions']){
        $subsessionIDs = json_decode($res['subsessions']);
        unset($res['subsessions']);
        $res['subsessions'] = array();
        foreach ($subsessionIDs as $sub) {
            $subsessionDetails = fetchDetails($sub, $link);
            array_push($res['subsessions'], $subsessionDetails);
        }
    }
    return $res;
}
function insertFeedback($id, $isSubsession, $parentSessionID, $data, $link) { //inserts the feedback data into db and sends notification mail, returns success message
    $details = dbSelectDetails($id, $link);
    if ($details['closed']) send_error_response("This feedback request has been closed by the facilitator.", 400);

    //insert the feedback for the main session
    if (!dbInsertFeedback($id, $data, $link)) send_error_response("dbInsertFeedback failed for an unknown reason", 500);
    
    //insert the feedback for the subsessions
    if (isset($data->subsessions)) {
        if ($isSubsession) send_error_response("session is being iterated as a subsession by insertFeedback but has subsessions itself", 500);
        foreach ($data->subsessions as $sub) {
            if ($sub->state == "Complete") {
                $subData = new stdClass();
                $subData->feedback = new stdClass();
                $subData->feedback->positive = $sub->positive;
                $subData->feedback->negative = $sub->negative;
                $subData->feedback->score = $sub->score;
                $subData->questions = array();
                if (!insertFeedback($sub->id, true, $id, $subData, $link)) send_error_response("dbInsertFeedback failed for an unknown reason (iterating subsession)", 500);
            }
        }
    }
    
    //send the email notifications
    $coolOff = new DateTime("-2 hours");
    $lastSent = new DateTime($details['lastSent']);
    if ($details['notifications'] && $details['email'] && $lastSent < $coolOff) {
        if ($isSubsession) {
            $parentDetails = dbSelectDetails($parentSessionID, $link);
            sendFeedbackNotification(true, $details['date'], $details['name'], $details['title'], $parentDetails['name'], $parentDetails['title'], $id, $details['email']);
        } else {
            sendFeedbackNotification(false, $details['date'], $details['name'], $details['title'], null, null, $id, $details['email']);
        }
        dbUpdateNotificationLastSent($id, $link);
    }

    return "insertFeedback successful";
}
function fetchCertificate($id, $name, $organisation, $link){
    $details = fetchDetails($id, $link);
    if ($details['closed']) send_error_response("This feedback request has been closed by the facilitator.", 400);
    if ($details['attendance']) dbInsertAttendance($id, $name, $organisation, $link);
    outputCertificate($details, htmlspecialchars_decode($name));
}

//view feedback
function fetchFeedback($id, $pin, $link){ //returns the session details and feedback for $id as json object
    $res = dbSelectFeedback($id, $link);
    if (!pinIsValid($pin, $res['pinHash'])) send_error_response("Invalid pin", 401);
    unset($res['email']);
    unset($res['lastSent']);
    unset($res['pinHash']);
    $res['name'] = htmlspecialchars_decode($res['name']);
    $res['title'] = htmlspecialchars_decode($res['title']);
    $res['date'] = formatDateHuman($res['date']);
    if ($res['subsessions']){
        $subsessionIDs = json_decode($res['subsessions']);
        unset($res['subsessions']);
        $res['subsessions'] = array();
        foreach ($subsessionIDs as $subID) {
            $sub = dbSelectFeedback($subID, $link);
            if (iconv_strlen($sub['subsessions'])>2) send_error_response("session [".$subID."] is being iterated as a subsession by fetchFeedback but has subsessions itself", 500);
            unset($sub['email']);
            unset($sub['lastSent']);
            unset($sub['pinHash']);
            array_push($res['subsessions'], json_encode($sub));
        }
    }
    $res['questions'] = organiseQuestionFeedback(json_decode($res['questions']),$res['questionFeedback']);
    unset($res['questionFeedback']);
    return $res;
}
function fetchFeedbackPDF($id, $pin, $subID, $link){ //returns the session feedback as a PDF document
    $feedback = fetchFeedback($id, $pin, $link);
    if ($subID) {
        $found = false;
        foreach ($feedback['subsessions'] as $subFeedbackJSON) {
            $subFeedback = json_decode($subFeedbackJSON,true);
            if ($subFeedback['id'] == $subID) {
                $subFeedback['subsessions'] = json_decode($subFeedback['subsessions']);
                $subFeedback['name'] = htmlspecialchars_decode($subFeedback['name']);
                $subFeedback['title'] = htmlspecialchars_decode($subFeedback['title']);
                $subFeedback['date'] = formatDateHuman($subFeedback['date']);
                $subFeedback['questions'] = array();
                outputFeedbackPDF($subFeedback);
                $found = true;
            }
        }
        if (!$found) send_error_response("Feedback PDF requested for session which is not part of this series.", 400);
    } else {
        outputFeedbackPDF($feedback);
    }
}

//view attendance
function fetchAttendance($id, $pin, $link){ //returns the session details and attendance for $id as json object
    $res = dbSelectAttendance($id, $link);
    if (!pinIsValid($pin, $res['pinHash'])) send_error_response("Invalid pin", 401);
    $res['attendees'] = buildAttendeesByOrg($res['names'], $res['organisations']);
    $res['attendeeCount'] = countAttendees($res['attendees']);
    if ($res['attendeeCount'] < 3) send_error_response("Cannot generate attendance report when less than 3 people have submitted feedback. This is to prevent attendees being linked to their feedback.", 401);
    foreach ($res['attendees'] as $key=>$org) if ($org['name'] == '') $res['attendees'][$key]['name'] = 'Organisation not specified';
    unset($res['email']);
    unset($res['lastSent']);
    unset($res['pinHash']);
    unset($res['names']);
    unset($res['organisations']);
    $res['date'] = formatDateHuman($res['date']);
    return $res;
}
function fetchAttendancePDF($id, $pin, $link){ //returns the session attendance as a PDF or CSV file
    $attendance = fetchAttendance($id, $pin, $link);
    outputAttendancePDF($attendance);
}
function fetchAttendanceCSV($id, $pin, $link){ //returns the session attendance as a PDF or CSV file
    $attendance = fetchAttendance($id, $pin, $link);
    outputAttendanceCSV($attendance);
}

//utilities
function checkEmailIsValid($data){
    if (!filter_var(json_decode($data), FILTER_VALIDATE_EMAIL)) send_error_response("Invalid email", 400);
    return "Email is valid";
}
function findMySessions($data, $link){
    $email = filter_var($data, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) send_error_response("Invalid email.", 401);
    $foundDetails = dbFetchDetailsByEmail($email, $link);
    if (count($foundDetails)>0){
        if (!sendFoundSessions($foundDetails, $email)) send_error_response("sendFoundSessions failed for an unknown reason", 500);
    }
    return "If any sessions were found matching your email the details have been emailed to you.";
}

//Live//
//session creation
function insertLiveSession($data, $link){ //inserts a new live session into the db, sends mail and returns a session id and pin
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
function insertLiveQuestion($question, $sessionID, $link){
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

//live
function fetchLiveDetails($id, $link){
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
function insertLiveSubmission($questionID, $submission, $link){
    
    //sanitize and validate
    $questionID = htmlspecialchars($questionID);
    if (strlen($questionID) == 0) send_error_response("questionID cannot be blank", 400);
    $submission = htmlspecialchars($submission);
    if (strlen($submission) == 0) send_error_response("Submission cannot be blank", 400);
        
    if (!dbInsertLiveSubmission($questionID, $submission, $link)) send_error_response("dbInsertLiveSubmission failed for an unknown reason", 500);

    if (!dbUpdateLiveQuestionLastSubmissionChecksum($questionID, crc32($submission), $link)) send_error_response("dbUpdateLiveQuestionLastSubmissionChecksum failed for an unknown reason", 500);

    return true;
}

//liveView
function fetchLiveViewDetails($id, $pin, $link){
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