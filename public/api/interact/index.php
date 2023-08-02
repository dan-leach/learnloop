<?php

require 'routes.php';

$link = mysqli_connect("localhost", "learnloop_app", $dbKey, "learnloop_data");

//select route
switch ($route) {
    //session creation
    case "insertSession":
        if (is_null($data) ) send_error_response("Variable(s) [data] must be defined for insertSession route", 400);
        echo json_encode(insertSession($data, $link));
        break;
    case "loadUpdateDetails":
        echo json_encode("todo");
        die();
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for loadUpdateDetails route", 400);
        echo json_encode(loadUpdateDetails($id, $pin, false, $link));
        break;
    case "updateDetails":
        echo json_encode("todo");
        die();
        if (is_null($id) || is_null($pin) || is_null($data)) send_error_response("Variable(s) [id], [pin] and [data] must be defined for updateDetails route", 400);
        echo json_encode(updateDetails($id, $pin, $data, false, false, null, null, null, $link));
        break;
    case "resetPIN":
        echo json_encode("todo");
        die();
        if (is_null($id) || is_null($data)) send_error_response("Variable(s) [id] and [data] must be defined for resetPin route", 400);
        echo json_encode(resetPin($id, $data, $link));
        break;
    case "closeSession":
        echo json_encode("todo");
        die();
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for closeSession route", 400);
        echo json_encode(closeSession($id, $pin, false, $link));
        break;

    case "fetchDetails":
        if (is_null($id)) send_error_response("Variable(s) [id] must be defined for fetchDetails route", 400);
        echo json_encode(fetchDetails($id, $link));
        break;
    case "insertSubmission":
        if (is_null($id) || is_null($data)) send_error_response("Variable(s) [id] and [data] must be defined for insertSubmission route", 400);
        echo json_encode(insertSubmission($id, $data, $link));
        break;

    case "fetchViewDetails":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for fetchViewDetails route", 400);
        echo json_encode(fetchViewDetails($id, $pin, $link));
        break;
    case "fetchQuestionSubmissions":
        echo json_encode("todo");
        die();
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for fetchQuestionSubmissions route", 400);
        echo json_encode(fetchFeedback($id, $pin, $link));
        break;
    case "checkQuestionNewSubmissions":
        echo json_encode("todo");
        die();
        break;
    
    default:
        send_error_response("Route [" . $route . "] not found",400);
}

mysqli_close($link);

?>