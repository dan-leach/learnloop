<?php

    //Open and check database connection
    require 'link.php';
    if($link === false) die("Session data could not be logged. The server returned the following error message: " . mysqli_connect_error());

    require 'functions.php';

    $id = htmlspecialchars($_GET['id']);
    
    $stmt = $link->prepare("SELECT name, date, title, questions, certificate, attendance, tags, subsessions FROM tbl_sessions_v3 WHERE id = ?");
    if ( false===$stmt ) die("Session data could not be retreived. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) die("Session data could not be retreived. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Session data could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $result = $stmt->get_result();
    if ( false===$result ) die("Session data could not be retrieved. The server returned the following error message: get_result() failed: " . mysqli_error($link));

    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        $rows = array();
        while($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
        $rows[0]['title'] = html_entity_decode($rows[0]['title']);
        $rows[0]['name'] = html_entity_decode($rows[0]['name']);
        $rows[0]['questions'] = $rows[0]['questions'];
    } else {
        $result->close();
        $stmt->close();

        $stmt = $link->prepare("SELECT sIdent FROM tbl_sessions WHERE sIdent = ?");
        if ( false===$stmt ) die("Session data could not be retreived. The server returned the following error message: prepare() failed: " . mysqli_error($link));

        $rc = $stmt->bind_param("s",$id);
        if ( false===$rc ) die("Session data could not be retreived. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

        $rc = $stmt->execute();
        if ( false===$rc ) die("Session data could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));

        $result = $stmt->get_result();
        if ( false===$result ) die("Session data could not be retrieved. The server returned the following error message: get_result() failed: " . mysqli_error($link));

        $row_cnt = $result->num_rows;

        if($row_cnt > 0){
            // found in v2
            die('{"redirect":"v2"}');
        } else {
            die("No session matching ID: ".$id);
        }

        $result->close();
        $stmt->close();
    }

    
    $subsessionIDs = json_decode($rows[0]['subsessions']);

    unset($rows[0]['subsessions']); //clear the subsession object ready to be repopulated with feedback data
    $i = 0;
    if ($subsessionIDs) {
        foreach ($subsessionIDs as $ssid) {
            $rows[0]['subsessions'][$i]['id'] = $ssid;
            
            $stmt = $link->prepare("SELECT name, title FROM tbl_sessions_v3 WHERE id = ?");
            if ( false===$stmt ) die("Subsession data could not be retreived. The server returned the following error message: prepare() failed: " . mysqli_error($link));
        
            $rc = $stmt->bind_param("s",$ssid);
            if ( false===$rc ) die("Subsession data could not be retreived. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
        
            $rc = $stmt->execute();
            if ( false===$rc ) die("Subsession data could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));
        
            $result = $stmt->get_result();
            if ( false===$result ) die("Subsession data could not be retrieved. The server returned the following error message: get_result() failed: " . mysqli_error($link));
        
            $row_cnt = $result->num_rows;
            if($row_cnt > 0){
                while($r = mysqli_fetch_assoc($result)) {
                    $rows[0]['subsessions'][$i]['name'] = html_entity_decode($r['name']);
                    $rows[0]['subsessions'][$i]['title'] = html_entity_decode($r['title']);
                }
            } else {
                die("No subsession matching ID: ".$ssid);
            }
            
            $i++;
        }
    }

    $rows[0]['date'] = formatDateHuman($rows[0]['date']);

    print json_encode($rows[0]);

    mysqli_close($link);


?>