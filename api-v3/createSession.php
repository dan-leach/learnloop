<?php
    //get the session data
    if (!isset($_POST['session'])) die("No session data received.");
    $session = json_decode($_POST['session']);

    //Open and check database connection
    require 'link.php';
    if($link === false) die("Session data could not be logged. The server returned the following error message: " . mysqli_connect_error());
    
    require 'functions.php';
    require 'createMail.php'; 

    // Generate unique short ID
    $id = createUniqueID($link);

    // Generate pin and hash
    $pin = createPin();
    $pinHash = pinHash($pin);

    $errMsg = "";

    //sanitize and validate
    $name = htmlspecialchars($session->name);
    if (strlen($name) == 0) $errMsg .= "Name is blank. ";
    $email = filter_var($session->email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errMsg .= "Email is not valid. ";
    $title = htmlspecialchars($session->title);
    if (strlen($title) == 0) $errMsg .= "Title is blank. ";
    $date = htmlspecialchars($session->date);
    if (!validateDate($date, 'Y-m-d')) {
        if(!validateDate($date, 'd/m/Y')){
            $errMsg .= "Date is not valid. ";
        } else {
            $d = substr($date, 0, 2);
            $m = substr($date, 3, 2);
            $y = substr($date, 6, 4);
            $date = date("Y-m-d", mktime(0, 0, 0, $m, $d, $y));;
        }
    }
    $questions = json_encode($session->questions);
    $certificate = filter_var($session->certificate, FILTER_VALIDATE_BOOLEAN);
    $notifications = filter_var($session->notifications, FILTER_VALIDATE_BOOLEAN);
    $attendance = filter_var($session->attendance, FILTER_VALIDATE_BOOLEAN);
    $tags = filter_var($session->tags, FILTER_VALIDATE_BOOLEAN);

    if (strlen($errMsg) > 0) die("Session data could not be logged. The server returned the following error message: " . $errMsg);

    $stmt = $link->prepare("INSERT INTO tbl_sessions_v3 (id, name, email, title, date, questions, certificate, notifications, attendance, tags, pinHash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ( false===$stmt ) die("Session data could not be logged. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("sssssssssss",$id, $name, $email, $title, $date, $questions, $certificate, $notifications, $attendance, $tags, $pinHash);
    if ( false===$rc ) die("Session data could not be logged. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Session data could not be logged. The server returned the following error message: execute() failed: " . mysqli_error($link));

    createMail($name, $date, $title, $email, "single", $session->questions, $notifications, $attendance, $tags, $certificate, $id, $pin, '', '', ''); //send email confirmation to the organiser

    echo '{"status":"200","msg":"new session insert into db successful","id":"'.$id.'","pin":"'.$pin.'","date":"'.formatDateHuman($date).'"}'; //respond to client with session ID and pin

    $stmt->close();
    mysqli_close($link);


?>