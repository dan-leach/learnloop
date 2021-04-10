<?php

    require 'link.php';
                    
    // Check connection
    if($link === false){
        die("Session data could not be logged. The server returned the following error message: " . mysqli_connect_error());
    }
    
    $fetchID = $_GET['fetchID'];

    // Attempt insert query execution
    /* $sql = "SELECT (sIdent, fName, fEmail, sName, sDate) FROM tbl_sessions WHERE (sIdent = $fetchID)";
    if(mysqli_query($link, $sql)){
        echo $uuid;
        $pass = "true";
    } else{
        echo "<br>Session data could not be logged. The server returned the following error message: " . mysqli_error($link);
        $pass = "false";
    } */

    if ($result = $link->query("SELECT sName, sDate, fName FROM tbl_sessions WHERE sIdent = '$fetchID'")) {
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
        /* close result set */
        $result->close();
    } else {
        echo "Error retrieving session details. The server returned the following error message: " . mysqli_error($link);
    }
        
    // Close connection
    mysqli_close($link);


?>