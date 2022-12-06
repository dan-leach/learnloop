<?php

    function emailFormatIsValid($email){ //returns true if email is properly formatted
        return (filter_var($email, FILTER_VALIDATE_EMAIL)) ? 'true' : 'false';
    }

    function createUniqueID($link){ // Generate unique short ID
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $pass = 0;
            $id = substr(str_shuffle($permitted_chars), 0, 6);    
            if ($result = $link->query("SELECT * FROM tbl_sessions_v3 WHERE id = '$id'")) {
                $row_cnt = $result->num_rows;
                if($row_cnt > 0){
                    $id = substr(str_shuffle($permitted_chars), 0, 6);
                } else {
                    $pass ++;
                }
                /* close result set */
                $result->close();
            } else {
                die("A new session ID could not be created. The server returned the following error message: " . mysqli_error($link));
            }
        } while ($pass < 1);
        return $id;
    }

    function createPin(){ // Generate pin
        $permitted_chars = '0123456789';
        return substr(str_shuffle($permitted_chars), 0, 6);    
    }

    function pinHash($pin){ //gets the hash for a given pin
        return hash("sha256", $pin);
    }

    if (!function_exists('str_contains')) {
        function str_contains($haystack, $needle) {
            return $needle !== '' && mb_strpos($haystack, $needle) !== false;
        }
    }
    
    function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    function formatDateHuman($date){
        $d = date_create($date);
        return date_format($d, 'd/m/Y');
    }

    function addSubsession($session, $link){
        //sanitize and validate
        $errMsg = '';

        $name = htmlspecialchars($session->name);
        if (strlen($name) == 0) $errMsg .= "Subsession name is blank. ";

        $email = filter_var($session->email, FILTER_SANITIZE_EMAIL);
        if (strlen($email) > 0){
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errMsg .= "Subsession email is not valid. ";
        }

        $title = htmlspecialchars($session->title);
        if (strlen($title) == 0) $errMsg .= "Subsession title is blank. ";
        
        $id = $session->id;
        if (strlen($id) == 0) $errMsg .= "Subsession ID is blank. ";

        if (strlen($errMsg) > 0) return ("Subsession session data could not be logged. The server returned the following error message: " . $errMsg);

        $date = $session->date;
        $notifications = true;
        $pinHash = $session->pinHash;

        $stmt = $link->prepare("INSERT INTO tbl_sessions_v3 (id, name, email, title, date, notifications, pinHash) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ( false===$stmt ) die("Subsession data could not be logged. The server returned the following error message: prepare() failed: " . mysqli_error($link));
    
        $rc = $stmt->bind_param("sssssss",$id, $name, $email, $title, $date, $notifications, $pinHash);
        if ( false===$rc ) die("Subsession data could not be logged. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
    
        $rc = $stmt->execute();
        if ( false===$rc ) die("Subsession data could not be logged. The server returned the following error message: execute() failed: " . mysqli_error($link));
        
        if (strlen($email) > 0) createMail($name, $date, $title, $email, "subsession", $notifications, '', $id, $session->pin, '', $session->seriesName, $session->seriesTitle);

        return $errMsg;
    }

    function feedbackSubsession($session, $seriesName, $seriesTitle, $link){
        //sanitize and validate
        $errMsg = '';

        $id = htmlspecialchars($session->id);
        $positive = htmlspecialchars($session->positive);
        if (strlen($positive) == 0) $errMsg .= "Subsession positive comments cannot be blank. ";
        $negative = htmlspecialchars($session->negative);
        if (strlen($negative) == 0) $errMsg .= "Subsession constructive comments cannot be blank. ";
        $score = filter_var($session->score, FILTER_SANITIZE_NUMBER_INT);
        if (!filter_var($score, FILTER_SANITIZE_NUMBER_INT)) $errMsg .= "Subsession score is not a valid number. ";

        if (strlen($errMsg) > 0) return ("Subsession feedback data could not be logged. The server returned the following error message: " . $errMsg);

        $stmt = $link->prepare("INSERT INTO tbl_feedback_v3 (id, positive, negative, score) VALUES (?, ?, ?, ?)");
        if ( false===$stmt ) die("Feedback data could not be logged. The server returned the following error message: prepare() failed: " . mysqli_error($link));

        $rc = $stmt->bind_param("ssss",$id, $positive, $negative, $score);
        if ( false===$rc ) die("Feedback data could not be logged. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

        $rc = $stmt->execute();
        if ( false===$rc ) die("Feedback data could not be logged. The server returned the following error message: execute() failed: " . mysqli_error($link));
        
        $title = '';
        $date = '';
        $name = '';
        $email = '';
        $notifications = '';
    
        $stmt = $link->prepare("SELECT title, date, name, email, notifications FROM tbl_sessions_v3 WHERE id = ?");
        if ( false===$stmt ) die("Your feedback was logged successfully but there was a problem sending it to the session facilitator as the session details required to send the message could not be retrieved. The server returned the following error message: prepare() failed: " . mysqli_error($link));
    
        $rc = $stmt->bind_param("s",$id);
        if ( false===$rc ) die("Your feedback was logged successfully but there was a problem sending it to the session facilitator as the session details required to send the message could not be retrieved. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
    
        $rc = $stmt->execute();
        if ( false===$rc ) die("Your feedback was logged successfully but there was a problem sending it to the session facilitator as the session details required to send the message could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));
    
        $result = $stmt->get_result();
        if ( false===$result ) die("Your feedback was logged successfully but there was a problem sending it to the session facilitator as the session details required to send the message could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));
    
        $row_cnt = $result->num_rows;
        if($row_cnt > 0){
            $rows = array();
            while($r = mysqli_fetch_assoc($result)) {
                $rows[] = $r;
                $title = $r['title'];
                $date = $r['date'];
                $name = $r['name'];
                $email = $r['email'];
                $notifications = $r['notifications'];
            }
        } else {
            die("Your feedback was logged successfully but there was a problem sending it to the session facilitator as the session details required to send the message could not be retrieved. The server returned the following error message: 'session ID not found'");
        }
    
        $result->close();
        $stmt->close();
        if ($notifications and $email) notificationMail($name, $date, $title, $email, $id, 'subsession', $seriesName, $seriesTitle);

        return $errMsg;
    }

?>