<?php

function dbSelectDetails($id, $link){ //returns the session details for $id as a php object
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("SELECT * FROM tbl_interact_sessions_dev WHERE id = ?");
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
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("SELECT facilitatorIndex FROM tbl_interact_sessions_dev WHERE id = ?");
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
    $res = array();
    if($link === false) send_error_response("database connection failed:" . mysqli_connect_error(), 500);
    
    $stmt = $link->prepare("SELECT * FROM tbl_interact_submissions_dev WHERE sessionId = ? AND interactionIndex = ? AND id > ?");
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
    $stmt = $link->prepare("UPDATE tbl_interact_sessions_dev SET facilitatorIndex = ? WHERE id = ?");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);
    $rc = $stmt->bind_param("ss",$newIndex, $id);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);
    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    return true;
}

function dbInsertSubmissiion($id, $interactionIndex, $response, $link){
    $stmt = $link->prepare("INSERT INTO tbl_interact_submissions_dev (sessionId, interactionIndex, response) VALUES (?, ?, ?)");
    if ( false===$stmt ) send_error_response("prepare() failed: " . mysqli_error($link), 500);

    $rc = $stmt->bind_param("sss",$id, $interactionIndex, $response);
    if ( false===$rc ) send_error_response("bind_param() failed: " . mysqli_error($link), 500);

    $rc = $stmt->execute();
    if ( false===$rc ) send_error_response("execute() failed: " . mysqli_error($link), 500);
    
    return true;
}


?>