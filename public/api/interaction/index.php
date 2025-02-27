<?php

require 'routes.php';

$link = mysqli_connect("localhost", "learnloop_app", $dbKey, "learnloop_data");

//select route
switch ($route) {
        //create
    case "insertSession":
        if (is_null($data)) send_error_response("Variable(s) [data] must be defined for insertSession route", 400);
        echo json_encode(insertSession($data, $link));
        break;
        //update
    case "resetPin":
        if (is_null($id) || is_null($data)) send_error_response("Variable(s) [id] and [data] must be defined for resetPin route", 400);
        echo json_encode(resetPin($id, $data, $link));
        break;
    case "updateSession":
        if (is_null($id) || is_null($pin) || is_null($data)) send_error_response("Variable(s) [id], [pin] and [data] must be defined for updateSession route", 400);
        echo json_encode(updateSession($id, $pin, $data, $link));
        break;
        //join
    case "fetchDetails":
        if (is_null($id)) send_error_response("Variable(s) [id] must be defined for fetchDetails route", 400);
        echo json_encode(fetchDetails($id, $link));
        break;
    case "insertSubmission":
        if (is_null($id) || is_null($data)) send_error_response("Variable(s) [id] and [data] must be defined for insertSubmission route", 400);
        echo json_encode(insertSubmission($id, $data, $link));
        break;
    case "fetchHostStatus":
        if (is_null($id)) send_error_response("Variable(s) [id] must be defined for fetchHostStatus route", 400);
        echo json_encode(fetchHostStatus($id, $link));
        break;
        //host
    case "fetchDetailsHost":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for fetchDetailsHost route", 400);
        echo json_encode(fetchDetailsHost($id, $pin, $link));
        break;
    case "fetchNewSubmissions":
        if (is_null($id) || is_null($pin) || is_null($data)) send_error_response("Variable(s) [id], [pin] and [data] must be defined for fetchNewSubmissions route", 400);
        echo json_encode(fetchNewSubmissions($id, $pin, $data, $link));
        break;
    case "updateHostStatus":
        if (is_null($id) || is_null($pin) || is_null($data)) send_error_response("Variable(s) [id], [pin] and [data] must be defined for updateHostStatus route", 400);
        echo json_encode(updateHostStatus($id, $pin, $data, $link));
        break;
    case "fetchSubmissionCount":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for fetchSubmissionCount route", 400);
        echo json_encode(fetchSubmissionCount($id, $pin, $link));
        break;
    case "deleteSubmissions":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for deleteSubmissions route", 400);
        echo json_encode(deleteSubmissions($id, $pin, $link));
        break;
        //utilities
    case "findMySessions":
        if (is_null($data)) send_error_response("Variable(s) [data] must be defined for findMySessions route", 400);
        echo json_encode(findMySessions($data, $link));
        break;
    default:
        send_error_response("Route [" . $route . "] not found in interaction module", 400);
}

mysqli_close($link);
