<?php

$tblSessions = 'tbl_interaction_sessions_v5';
$tblSubmissions = 'tbl_interaction_submissions_v5';
$tblFeedbackSessions = 'tbl_feedback_sessions_v5';

function dbSessionExists($id, $link)
{ //returns true if $id already exists in table
    global $tblSessions;
    if ($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT * FROM $tblSessions WHERE id = ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s", $id);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if (false === $result) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $row_cnt = $result->num_rows;
    $res = ($row_cnt > 0);

    $result->close();
    $stmt->close();

    return $res;
}

function dbFeedbackSessionExists($id, $link)
{ //returns true if $id already exists in table
    global $tblFeedbackSessions;
    if ($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT * FROM $tblFeedbackSessions WHERE id = ?");

    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s", $id);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if (false === $result) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $row_cnt = $result->num_rows;
    $res = ($row_cnt > 0);

    $result->close();
    $stmt->close();

    return $res;
}

function dbInsertSession($id, $name, $email, $title, $feedbackID, $slides, $pinHash, $link)
{ //inserts session $data with $id and $pinHash
    global $tblSessions;
    $stmt = $link->prepare("INSERT INTO $tblSessions (id, name, email, title, feedbackID, slides, pinHash) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("sssssss", $id, $name, $email, $title, $feedbackID, $slides, $pinHash);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    return true;
}

function dbUpdateSession($id, $name, $email, $title, $feedbackID, $slides, $link)
{ //inserts session $data with $id and $pinHash
    global $tblSessions;
    $stmt = $link->prepare("UPDATE $tblSessions SET name = ?, email = ?, title = ?, feedbackID = ?, slides = ? WHERE id = ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("ssssss", $name, $email, $title, $feedbackID, $slides, $id);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    return true;
}

function dbSelectDetails($id, $link)
{ //returns the session details for $id as a php object
    global $tblSessions;
    if ($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT * FROM $tblSessions WHERE id = ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s", $id);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if (false === $result) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $row_cnt = $result->num_rows;
    if ($row_cnt > 0) {
        while ($r = mysqli_fetch_assoc($result)) {
            $res = $r;
        }
    } else {
        send_error_response("No session matching ID: " . $id, 404);
    }

    $result->close();
    $stmt->close();

    return $res;
}

function dbSelectHostStatus($id, $link)
{ //returns the facilitator Index for $id as a php object
    global $tblSessions;
    if ($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT hostStatus FROM $tblSessions WHERE id = ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s", $id);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if (false === $result) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $row_cnt = $result->num_rows;
    if ($row_cnt > 0) {
        while ($r = mysqli_fetch_assoc($result)) {
            $res = $r;
        }
    } else {
        send_error_response("No session matching ID: " . $id, 404);
    }

    $result->close();
    $stmt->close();

    return $res;
}

function dbSelectNewSubmissions($id, $slideIndex, $lastSubmissionId, $link)
{ //returns the session details for $id as a php object
    global $tblSubmissions;
    $res = array();
    if ($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT * FROM $tblSubmissions WHERE sessionId = ? AND slideIndex = ? AND id > ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("sss", $id, $slideIndex, $lastSubmissionId);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if (false === $result) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $row_cnt = $result->num_rows;
    if ($row_cnt > 0) {
        while ($r = mysqli_fetch_assoc($result)) {
            $r['response'] = htmlspecialchars_decode($r['response']);
            array_push($res, $r);
        }
    }

    $result->close();
    $stmt->close();

    return $res;
}

function dbUpdateHostStatus($id, $hostStatus, $link)
{
    global $tblSessions;
    $stmt = $link->prepare("UPDATE $tblSessions SET hostStatus = ? WHERE id = ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);
    $rc = $stmt->bind_param("ss", $hostStatus, $id);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);
    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);
    return true;
}

function dbInsertSubmissiion($id, $slideIndex, $response, $link)
{
    global $tblSubmissions;
    $stmt = $link->prepare("INSERT INTO $tblSubmissions (sessionId, slideIndex, response) VALUES (?, ?, ?)");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("sss", $id, $slideIndex, $response);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    return true;
}

function dbCountSubmissions($id, $link)
{
    global $tblSubmissions;
    if ($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT COUNT(id) AS submissionCount FROM $tblSubmissions WHERE sessionId = ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s", $id);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if (false === $result) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $row_cnt = $result->num_rows;
    if ($row_cnt > 0) {
        while ($r = mysqli_fetch_assoc($result)) {
            $res = $r;
        }
    }

    $result->close();
    $stmt->close();

    return $res['submissionCount'];
}

function dbDeleteSubmissions($id, $link)
{
    global $tblSubmissions;
    if ($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("DELETE FROM $tblSubmissions WHERE sessionId = ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s", $id);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $stmt->close();

    return true;
}

function dbUpdatePinHash($id, $pinHash, $link)
{ //updates the pinhash for $id
    global $tblSessions;
    $stmt = $link->prepare("UPDATE $tblSessions SET pinHash = ? WHERE id = ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);
    $rc = $stmt->bind_param("ss", $pinHash, $id);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);
    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);
    return true;
}

function dbFetchDetailsByEmail($email, $link)
{
    global $tblSessions;
    if ($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT * FROM $tblSessions WHERE email = ?");
    if (false === $stmt) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s", $email);
    if (false === $rc) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if (false === $rc) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if (false === $result) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $res = array();
    $row_cnt = $result->num_rows;
    if ($row_cnt > 0) {
        while ($r = mysqli_fetch_assoc($result)) {
            array_push($res, $r);
        }
    }

    $result->close();
    $stmt->close();

    return $res;
}
