<?php

function dbSessionExists($id, $link){ //returns true if $id already exists in table
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT * FROM tbl_sessions_v4 WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if ( false===$result ) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $row_cnt = $result->num_rows;
    $res = ($row_cnt > 0);

    $result->close();
    $stmt->close();
    
    return $res;
}

function dbInsertSession($id, $name, $email, $title, $date, $questions, $certificate, $subsessions, $isSubsession, $notifications, $attendance, $tags, $pinHash, $link) { //inserts session $data with $id and $pinHash
    $stmt = $link->prepare("INSERT INTO tbl_sessions_v4 (id, name, email, title, date, questions, certificate, subsessions, isSubsession, notifications, attendance, tags, pinHash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("sssssssssssss",$id, $name, $email, $title, $date, $questions, $certificate, $subsessions, $isSubsession, $notifications, $attendance, $tags, $pinHash);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    
    return true;
}

function dbSelectDetails($id, $link){ //returns the session details for $id as a php object
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("SELECT * FROM tbl_sessions_v4 WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if ( false===$result ) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        while($r = mysqli_fetch_assoc($result)) {
            $res = $r;
        }
    } else {
        send_error_response("No session matching ID: ".$id, 404);
    }

    $result->close();
    $stmt->close();

    return $res;
}

function dbUpdateDetails($id, $name, $email, $title, $date, $questions, $certificate, $subsessions, $notifications, $attendance, $tags, $link){ //updates the session with new data
    $stmt = $link->prepare("UPDATE tbl_sessions_v4 SET name = ?, email = ?, title = ?, date = ?, questions = ?, certificate = ?, subsessions = ?, notifications = ?, attendance = ?, tags = ? WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);
    $rc = $stmt->bind_param("sssssssssss", $name, $email, $title, $date, $questions, $certificate, $subsessions, $notifications, $attendance, $tags, $id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);
    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    return true;
}

function dbInsertFeedback($id, $data, $link){ //inserts data into feedback for $id
    $errMsg = "";
    $positive = htmlspecialchars($data->feedback->positive);
    if (strlen($positive) == 0) $errMsg .= "Positive comments cannot be blank. ";
    $negative = htmlspecialchars($data->feedback->negative);
    if (strlen($negative) == 0) $errMsg .= "Constructive comments cannot be blank. ";
    $score = filter_var($data->feedback->score, FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($score, FILTER_SANITIZE_NUMBER_INT)) $errMsg .= "Score is not a valid number. ";
    $tags = (isset($data->tags)) ? json_encode($data->tags) : '[]';
    $questions = ($data->questions) ? json_encode($data->questions) : '[]';

    if (strlen($errMsg) > 0) send_error_response($errMsg, 400);

    $stmt = $link->prepare("INSERT INTO tbl_feedback_v4 (id, positive, negative, questions, score, tags) VALUES (?, ?, ?, ?, ?, ?)");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("ssssss",$id, $positive, $negative, $questions, $score, $tags);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    
    return true;
}

function dbUpdateNotificationLastSent($id, $link){ //updates the notification lastSent datetime to now for $id
    $stmt = $link->prepare("UPDATE tbl_sessions_v4 SET lastSent = NOW() WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);
    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);
    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    return true;
}

function dbSelectFeedback($id, $link){ //returns the session details for $id as a php object
    
    $res = dbSelectDetails($id, $link);

    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT * FROM tbl_feedback_v4 WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if ( false===$result ) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $row_cnt = $result->num_rows;
    $res['positive'] = array();
    $res['negative'] = array();
    $res['questionFeedback'] = array();
    $res['score'] = array();
    if($row_cnt > 0){
        while($r = mysqli_fetch_assoc($result)) {
            array_push($res['positive'], html_entity_decode($r['positive']));
            array_push($res['negative'], html_entity_decode($r['negative']));
            array_push($res['questionFeedback'], json_decode($r['questions']));
            array_push($res['score'], html_entity_decode($r['score']));
        }
    } else {
        array_push($res['positive'], "No feedback found.");
        array_push($res['negative'], "No feedback found.");
        array_push($res['questionFeedback'], "No feedback found.");
        array_push($res['score'], "No feedback found.");
    }

    $result->close();
    $stmt->close();

    return $res;
}

function dbSelectAttendance($id, $link){ //returns the session attendance for $id as a php object
    $res = dbSelectDetails($id, $link);

    if (!$res['attendance']) send_error_response("The attendance register for this session is disabled.", 400);

    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT name, organisation FROM tbl_attendance_v4 WHERE id = ? ORDER BY organisation, name");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if ( false===$result ) send_error_response("get_result() failed: " . mysqli_error($link), 500);
    
    $row_cnt = $result->num_rows;
    $res['names'] = array();
    $res['organisations'] = array();

    if($row_cnt > 0){
        while($r = mysqli_fetch_assoc($result)) {
            array_push($res['names'], html_entity_decode($r['name']));
            array_push($res['organisations'], html_entity_decode($r['organisation']));
        }
    }

    $result->close();
    $stmt->close();

    return $res;
}

function dbSetNotificationPreference($id, $data, $link){ //updates the notifications for $id as per $data
    $stmt = $link->prepare("UPDATE tbl_sessions_v4 SET notifications = ? WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);
    $rc = $stmt->bind_param("ss",$data, $id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);
    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    return true;
}

function dbUpdatePinHash($id, $pinHash, $link){ //updates the pinhash for $id
    $stmt = $link->prepare("UPDATE tbl_sessions_v4 SET pinHash = ? WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);
    $rc = $stmt->bind_param("ss",$pinHash, $id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);
    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    return true;
}

function dbCloseSession($id, $link){ //closes a session for $id
    $stmt = $link->prepare("UPDATE tbl_sessions_v4 SET closed = true WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);
    $rc = $stmt->bind_param("s", $id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);
    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    return true;
}

function dbFetchDetailsByEmail($email, $link){
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("SELECT * FROM tbl_sessions_v4 WHERE email = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s",$email);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if ( false===$result ) send_error_response("get_result() failed: " . mysqli_error($link), 500);

    $res = array();
    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        while($r = mysqli_fetch_assoc($result)) {
            array_push($res, $r);
        }
    }

    $result->close();
    $stmt->close();

    return $res;
}

function dbInsertAttendance($id, $name, $organisation, $link){
    $stmt = $link->prepare("INSERT INTO tbl_attendance_v4 (id, name, organisation) VALUES (?, ?, ?)");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("sss",$id, $name, $organisation);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    
    return true;
}

?>