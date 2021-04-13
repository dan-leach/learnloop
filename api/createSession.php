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

    $errMsg = "";

    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    //get sanitized POST variables and validate
    $fName = filter_var($_POST['fName'], FILTER_SANITIZE_STRING);
    if (strlen($fName) == 0) {
        $errMsg = "Facilitator name is blank; ";
    }
    $fEmail = filter_var($_POST['fEmail'], FILTER_SANITIZE_EMAIL);
    if (filter_var($fEmail, FILTER_VALIDATE_EMAIL)) {
        //valid email
    } else {
        $errMsg = $errMsg . "Facilitator email is not valid; ";
    }
    $sName = filter_var($_POST['sName'], FILTER_SANITIZE_STRING);
    if (strlen($sName) == 0) {
        $errMsg = $errMsg . "Session name is blank; ";
    }
    $sDate = filter_var($_POST['sDate'], FILTER_SANITIZE_STRING);
    if (!validateDate($sDate, 'Y-m-d')) {
        $errMsg = $errMsg . "Session date is not valid; ";
    }
    $sCert = filter_var($_POST['sCert'], FILTER_VALIDATE_BOOLEAN);

    if (strlen($errMsg) > 0) {
        die("Session data could not be logged. The server returned the following error message: " . $errMsg);
    }

    // Attempt insert query execution
    $sql = "INSERT INTO tbl_sessions (ID, sIdent, fName, fEmail, sName, sDate, sCert) VALUES (null, '$uuid', '$fName', '$fEmail', '$sName', '$sDate', '$sCert')";
    if(mysqli_query($link, $sql)){
        echo '{"status":"200","msg":"new session insert into db successful","uuid":"'.$uuid.'"}';
    } else{
        echo "Session data could not be logged. The server returned the following error message: " . mysqli_error($link);
    }
        
    // Close connection
    mysqli_close($link);


?>