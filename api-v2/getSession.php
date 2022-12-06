<?php

    require 'link.php';
                    
    // Check connection
    if($link === false){
        die("Session data could not be logged. The server returned the following error message: " . mysqli_connect_error());
    }
    
    $fetchID = filter_var($_GET['fetchID'], FILTER_SANITIZE_STRING);
    
    $stmt = $link->prepare("SELECT sName, sDate, fName, sCert FROM tbl_sessions WHERE sIdent = ?");
    if ( false===$stmt ) {
        die("Session data could not be retreived. The server returned the following error message: prepare() failed: " . mysqli_error($link));
    }

    $rc = $stmt->bind_param("s",$fetchID);
    if ( false===$rc ) {
        die("Session data could not be retreived. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
    }

    $rc = $stmt->execute();
    if ( false===$rc ) {
        die("Session data could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));
    }

    $result = $stmt->get_result();
    if ( false===$result ) {
        die("Session data could not be retrieved. The server returned the following error message: get_result() failed: " . mysqli_error($link));
    }

    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        $rows = array();
        while($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
        print json_encode($rows);
    } else {
        echo "No session matching ID: ".$fetchID;
    }

    $result->close();
    $stmt->close();
    mysqli_close($link);


?>