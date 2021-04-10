<?php

    require 'link.php';
                    
    // Check connection
    if($link === false){
        die("Session data could not be logged. The server returned the following error message: " . mysqli_connect_error());
    }
    
    // Generate unique short ID
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    do {
        $pass = 0;
        $uuid = substr(str_shuffle($permitted_chars), 0, 6);    
        if ($result = $link->query("SELECT * FROM tbl_sessions WHERE sIdent = '$uuid'")) {
            $row_cnt = $result->num_rows;
            if($row_cnt > 0){
                $uuid = substr(str_shuffle($permitted_chars), 0, 6);
            } else {
                $pass ++;
            }
            /* close result set */
            $result->close();
        } else {
            echo "A new session ID could not be created. The server returned the following error message: " . mysqli_error($link);
        }
    } while ($pass < 1);

    //convert variables into strings before SQL insert
    $sIdent = "'" . $uuid . "'";
    $fName = "'" . $_POST['fName'] . "'";
    $fEmail = "'" . $_POST['fEmail'] . "'";
    $sName = "'" . $_POST['sName'] . "'";
    $sDate = "'" . $_POST['sDate'] . "'";

    // Attempt insert query execution
    $sql = "INSERT INTO tbl_sessions (ID, sIdent, fName, fEmail, sName, sDate) VALUES (null, $sIdent, $fName, $fEmail, $sName, $sDate)";
    if(mysqli_query($link, $sql)){
        echo '{"status":"200","msg":"new session insert into db successful","uuid":"'.$uuid.'"}';
    } else{
        echo "Session data could not be logged. The server returned the following error message: " . mysqli_error($link);
    }
        
    // Close connection
    mysqli_close($link);


?>