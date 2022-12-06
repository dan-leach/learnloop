<?php

    // Open and check connection
    require 'link.php';
    if($link === false) die("Session data could not be logged. The server returned the following error message: " . mysqli_connect_error());

    require 'functions.php';
    
    $id = htmlspecialchars($_POST['id']);
    $pin = htmlspecialchars($_POST['pin']);
    $pinHash = pinHash($pin);
    
    $stmt = $link->prepare("SELECT name, title, date, subsessions, pinHash FROM tbl_sessions_v3 WHERE id = ?");
    if ( false===$stmt ) die("Session data could not be retreived. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) die("Session data could not be retreived. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Session data could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $result = $stmt->get_result();
    if ( false===$result ) die("Session data could not be retrieved. The server returned the following error message: get_result() failed: " . mysqli_error($link));

    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        $sessionData = array();
        while($r = mysqli_fetch_assoc($result)) {
            $sessionData[] = $r;
        }
        $sessionData[0]['title'] = html_entity_decode($sessionData[0]['title']);
        $sessionData[0]['name'] = html_entity_decode($sessionData[0]['name']);
        $sessionData[0]['date'] = formatDateHuman($sessionData[0]['date']);
    } else {
        die("No session matching ID: ".$id);
    }
    
    if ($pinHash != $sessionData[0]['pinHash']) die("Incorrect PIN"); //stop if wrong pin
    unset($sessionData[0]['pinHash']); //remove the pinHash from the array to be returned to client

    $sessionData[0]['subsessionIDs'] = json_decode($sessionData[0]['subsessions']); //convert subsession IDs stored as json back into array
    unset($sessionData[0]['subsessions']); //clear the subsession object ready to be repopulated with feedback data
    
    $stmt = $link->prepare("SELECT positive, negative, score FROM tbl_feedback_v3 WHERE id = ?");
    if ( false===$stmt ) die("Session data could not be retreived. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) die("Session data could not be retreived. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Session data could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $result = $stmt->get_result();
    if ( false===$result ) die("Session data could not be retrieved. The server returned the following error message: get_result() failed: " . mysqli_error($link));

    $row_cnt = $result->num_rows;
    $sessionData[0]['positive'] = array();
    $sessionData[0]['negative'] = array();
    $sessionData[0]['score'] = array();
    if($row_cnt > 0){
        while($r = mysqli_fetch_assoc($result)) {
            array_push($sessionData[0]['positive'], html_entity_decode($r['positive']));
            array_push($sessionData[0]['negative'], html_entity_decode($r['negative']));
            array_push($sessionData[0]['score'], html_entity_decode($r['score']));
        }
    } else {
        array_push($sessionData[0]['positive'], "No feedback found.");
        array_push($sessionData[0]['negative'], "No feedback found.");
        array_push($sessionData[0]['score'], "No feedback found.");
    }

    if ($sessionData[0]['subsessionIDs']) {
        foreach ($sessionData[0]['subsessionIDs'] as $key=>$subID) {
    
            $stmt = $link->prepare("SELECT name, title FROM tbl_sessions_v3 WHERE id = ?");
            if ( false===$stmt ) die("Session data could not be retreived. The server returned the following error message: prepare() failed: " . mysqli_error($link));
        
            $rc = $stmt->bind_param("s",$subID);
            if ( false===$rc ) die("Session data could not be retreived. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
        
            $rc = $stmt->execute();
            if ( false===$rc ) die("Session data could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));
        
            $result = $stmt->get_result();
            if ( false===$result ) die("Session data could not be retrieved. The server returned the following error message: get_result() failed: " . mysqli_error($link));
        
            $row_cnt = $result->num_rows;
            if($row_cnt > 0){
                while($r = mysqli_fetch_assoc($result)) {
                    $sessionData[0]['subsessions'][$key]['name'] = html_entity_decode($r['name']);
                    $sessionData[0]['subsessions'][$key]['title'] = html_entity_decode($r['title']);
                }
            }
    
            $sessionData[0]['subsessions'][$key]['id'] = $subID;
            $stmt = $link->prepare("SELECT positive, negative, score FROM tbl_feedback_v3 WHERE id = ?");
            if ( false===$stmt ) die("Session data could not be retreived. The server returned the following error message: prepare() failed: " . mysqli_error($link));
    
            $rc = $stmt->bind_param("s",$subID);
            if ( false===$rc ) die("Session data could not be retreived. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
    
            $rc = $stmt->execute();
            if ( false===$rc ) die("Session data could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));
    
            $result = $stmt->get_result();
            if ( false===$result ) die("Session data could not be retrieved. The server returned the following error message: get_result() failed: " . mysqli_error($link));
    
            $row_cnt = $result->num_rows;
            $sessionData[0]['subsessions'][$key]['positive'] = array();
            $sessionData[0]['subsessions'][$key]['negative'] = array();
            $sessionData[0]['subsessions'][$key]['score'] = array();
            if($row_cnt > 0){
                while($r = mysqli_fetch_assoc($result)) {
                    array_push($sessionData[0]['subsessions'][$key]['positive'], html_entity_decode($r['positive']));
                    array_push($sessionData[0]['subsessions'][$key]['negative'], html_entity_decode($r['negative']));
                    array_push($sessionData[0]['subsessions'][$key]['score'], html_entity_decode($r['score']));
                }
            } else {
                array_push($sessionData[0]['subsessions'][$key]['positive'], "No feedback found.");
                array_push($sessionData[0]['subsessions'][$key]['negative'], "No feedback found.");
                array_push($sessionData[0]['subsessions'][$key]['score'], "No feedback found.");
            }
        }
    }
    
    unset($sessionData[0]['subsessionIDs']); //clear the subsessionIDs once no longer required
    print json_encode($sessionData[0]); //return the array to the client

    $result->close();
    $stmt->close();
    mysqli_close($link);


?>