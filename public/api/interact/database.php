<?php

$tblSessions = 'tbl_interact_sessions_dev';
$tblSubmissions = 'tbl_interact_submissions_dev';

function dbSessionExists($id, $link){ //returns true if $id already exists in table
    global $tblSessions;
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);

    $stmt = $link->prepare("SELECT * FROM $tblSessions WHERE id = ?");
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

function dbInsertSession($id, $name, $email, $title, $interactions, $pinHash, $link) { //inserts session $data with $id and $pinHash
    global $tblSessions;
    $stmt = $link->prepare("INSERT INTO $tblSessions (id, name, email, title, interactions, pinHash) VALUES (?, ?, ?, ?, ?, ?)");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("ssssss",$id, $name, $email, $title, $interactions, $pinHash);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    
    return true;
}

function dbSelectDetails($id, $link){ //returns the session details for $id as a php object
    global $tblSessions;
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("SELECT * FROM $tblSessions WHERE id = ?");
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

function dbSelectFacilitatorIndex($id, $link){ //returns the facilitator Index for $id as a php object
    global $tblSessions;
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("SELECT facilitatorIndex FROM $tblSessions WHERE id = ?");
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

function dbSelectNewSubmissions($id, $interactionIndex, $lastSubmissionId, $link){ //returns the session details for $id as a php object
    global $tblSubmissions;
    $res = array();
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("SELECT * FROM $tblSubmissions WHERE sessionId = ? AND interactionIndex = ? AND id > ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("sss",$id,$interactionIndex, $lastSubmissionId);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $result = $stmt->get_result();
    if ( false===$result ) send_error_response("get_result() failed: " . mysqli_error($link), 500);

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

function dbUpdateFacilitatorIndex($id, $newIndex, $link) {
    global $tblSessions;
    $stmt = $link->prepare("UPDATE $tblSessions SET facilitatorIndex = ? WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);
    $rc = $stmt->bind_param("ss",$newIndex, $id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);
    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    return true;
}

function dbInsertSubmissiion($id, $interactionIndex, $response, $link){
    global $tblSubmissions;
    $stmt = $link->prepare("INSERT INTO $tblSubmissions (sessionId, interactionIndex, response) VALUES (?, ?, ?)");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("sss",$id, $interactionIndex, $response);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    
    return true;
}

function dbCountSubmissions($id, $link){
    global $tblSubmissions;
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("SELECT COUNT(id) AS submissionCount FROM $tblSubmissions WHERE sessionId = ?");
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
    }

    $result->close();
    $stmt->close();

    return $res['submissionCount'];
}

function dbDeleteSubmissions($id, $link){
    global $tblSubmissions;
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("DELETE FROM $tblSubmissions WHERE sessionId = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);

    $stmt->close();

    return true;
}


?>