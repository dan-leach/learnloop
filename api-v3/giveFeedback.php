<?php

    //Open and check database connection
    require 'link.php';
    if($link === false) die("Session data could not be logged. The server returned the following error message: " . mysqli_connect_error());

    require 'functions.php';
    require 'createMail.php'; 

    if (!isset($_POST['session'])) die("No feedback data received.");
    $session = json_decode($_POST['session']);
    
    $errMsg = "";

    $id = htmlspecialchars($session->id);
    $positive = htmlspecialchars($session->feedback->positive);
    if (strlen($positive) == 0) $errMsg .= "Positive comments cannot be blank. ";
    $negative = htmlspecialchars($session->feedback->negative);
    if (strlen($negative) == 0) $errMsg .= "Constructive comments cannot be blank. ";
    $score = filter_var($session->feedback->score, FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($score, FILTER_SANITIZE_NUMBER_INT)) $errMsg .= "Score is not a valid number. ";

    if (strlen($errMsg) > 0) die("Feedback data could not be logged. The server returned the following error message: " . $errMsg);

    $stmt = $link->prepare("INSERT INTO tbl_feedback_v3 (id, positive, negative, score) VALUES (?, ?, ?, ?)");
    if ( false===$stmt ) die("Feedback data could not be logged. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("ssss",$id, $positive, $negative, $score);
    if ( false===$rc ) die("Feedback data could not be logged. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Feedback data could not be logged. The server returned the following error message: execute() failed: " . mysqli_error($link));

    //initialize variables outside scope of sql query function
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

    mysqli_close($link);

    if ($notifications && $email) {
        notificationMail($name, $date, $title, $email, $id,'','','');
        echo '{"status":"200","msg":"feedback insert into db successful; notification to facilitator sent"}';
    } else {
        echo '{"status":"200","msg":"feedback insert into db successful; notifications disabled"}';
    }
    
?>