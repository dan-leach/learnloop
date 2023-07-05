<?php

use PgSql\Lob;

function handle_error($error){
    sendMail(addHeader().$error."<br><br>".addFooter(false), 'LearnLoop error notification', 'mail@learnloop.co.uk', 'LearnLoop');
    http_response_code(500);
    die(json_encode('Sorry, an unexpected error has occurred. This event has been logged. If you keep seeing this message please contact mail@learnloop.co.uk including the error message below and a description of what you were doing when it appeared. '.preg_replace('/[[:cntrl:]]/', '', $error)));
}
set_exception_handler('handle_error');

function send_error_response($msg, $status){
    http_response_code($status);
    die(json_encode($msg));
}

if (!isset($_POST['route'])) send_error_response("Route must be defined", 400);
$route = htmlspecialchars($_POST['route']);

//not all routes require all of the variables below - these are checked at the router level
if (isset($_POST['id'])) $id = htmlspecialchars($_POST['id']);
if (isset($_POST['pin'])) $pin = htmlspecialchars($_POST['pin']);
if (isset($_POST['data'])) $data = json_decode($_POST['data']); //data must be sanitised later

require 'private/keys.php';
require 'routes.php';

$link = mysqli_connect("localhost", "learnloop_app", $dbKey, "learnloop_data");

//select route
switch ($route) {
    //session creation
    case "insertSession":
        if (is_null($data) ) send_error_response("Variable(s) [data] must be defined for insertSession route", 400);
        echo json_encode(insertSession($data, false, null, null, null, $link));
        break;

    //session updates
    case "loadUpdateDetails":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for loadUpdateDetails route", 400);
        echo json_encode(loadUpdateDetails($id, $pin, false, $link));
        break;
    case "updateDetails":
        if (is_null($id) || is_null($pin) || is_null($data)) send_error_response("Variable(s) [id], [pin] and [data] must be defined for updateDetails route", 400);
        echo json_encode(updateDetails($id, $pin, $data, false, false, null, null, null, $link));
        break;
    case "setNotificationPreference":
        if (is_null($id) || is_null($pin) || is_null($data)) send_error_response("Variable(s) [id], [pin] and [data] must be defined for setNotificationPreference route", 400);
        echo json_encode(setNotificationPreference($id, $pin, $data, $link));
        break;
    case "resetPIN":
        if (is_null($id) || is_null($data)) send_error_response("Variable(s) [id] and [data] must be defined for resetPin route", 400);
        echo json_encode(resetPin($id, $data, $link));
        break;
    case "closeSession":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for closeSession route", 400);
        echo json_encode(closeSession($id, $pin, false, $link));
        break;

    //give feedback
    case "fetchDetails":
        if (is_null($id)) send_error_response("Variable(s) [id] must be defined for fetchSession route", 400);
        echo json_encode(fetchDetails($id, $link));
        break;
    case "insertFeedback":
        if (is_null($id) || is_null($data)) send_error_response("Variable(s) [id] and [data] must be defined for insertFeedback route", 400);
        echo json_encode(insertFeedback($id, false, null, $data, $link));
        break;
    case "fetchCertificate":
        if (isset($_POST['name'])) $name = htmlspecialchars($_POST['name']);
        if (isset($_POST['organisation'])) $organisation = htmlspecialchars($_POST['organisation']);
        if (is_null($id) || is_null($name) || is_null($organisation)) send_error_response("Variable(s) [id], [name] and [organisation] must be defined for fetchCertificate route", 400);
        fetchCertificate($id, $name, $organisation, $link);
        break;

    //view feedback
    case "fetchFeedback":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for fetchFeedback route", 400);
        echo json_encode(fetchFeedback($id, $pin, $link));
        break;
    case "fetchFeedbackPDF":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for fetchFeedback route", 400);
        $subID = '';
        if (isset($_POST['subID'])) $subID = htmlspecialchars($_POST['subID']);
        echo fetchFeedbackPDF($id, $pin, $subID, $link);
        break;

    //view attendance
    case "fetchAttendance":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for fetchAttendance route", 400);
        echo json_encode(fetchAttendance($id, $pin, $link));
        break;
    case "fetchAttendancePDF":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for fetchAttendancePDF route", 400);
        echo json_encode(fetchAttendancePDF($id, $pin, $link));
        break;
    case "fetchAttendanceCSV":
        if (is_null($id) || is_null($pin)) send_error_response("Variable(s) [id] and [pin] must be defined for fetchAttendanceCSV route", 400);
        echo json_encode(fetchAttendanceCSV($id, $pin, $link));
        break;

    //utilities
    case "checkEmailIsValid":
        if (is_null($data)) send_error_response("Variable(s) [data] must be defined for checkEmailIsValid route", 400);
        echo json_encode(checkEmailIsValid($data));
        break;
    case "findMySessions":
        if (is_null($data)) send_error_response("Variable(s) [data] must be defined for findMySessions route", 400);
        echo json_encode(findMySessions($data, $link));
        break;
    default:
        send_error_response("Route [" . $route . "] not found",400);
}

mysqli_close($link);

?>