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
        if(!validateDate($sDate, 'd/m/Y')){
            $errMsg = $errMsg . "Session date is not valid; ";
        } else {
            $d = substr($sDate, 0, 2);
            $m = substr($sDate, 3, 2);
            $y = substr($sDate, 6, 4);
            $sDate = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));;
        }
    }
    $sCert = filter_var($_POST['sCert'], FILTER_VALIDATE_BOOLEAN);

    if (strlen($errMsg) > 0) {
        die("Session data could not be logged. The server returned the following error message: " . $errMsg);
    }

    $stmt = $link->prepare("INSERT INTO tbl_sessions (sIdent, fName, fEmail, sName, sDate, sCert) VALUES (?, ?, ?, ?, ?, ?)");
    if ( false===$stmt ) {
        die("Session data could not be logged. The server returned the following error message: prepare() failed: " . mysqli_error($link));
    }

    $rc = $stmt->bind_param("ssssss",$uuid, $fName, $fEmail, $sName, $sDate, $sCert);
    if ( false===$rc ) {
        die("Session data could not be logged. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
    }

    $rc = $stmt->execute();
    if ( false===$rc ) {
        die("Session data could not be logged. The server returned the following error message: execute() failed: " . mysqli_error($link));
    }

    require 'createMail.php'; 
    echo '{"status":"200","msg":"new session insert into db successful","uuid":"'.$uuid.'"}';

    $stmt->close();
    mysqli_close($link);


?>