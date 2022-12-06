<?php

    //get the session data
    if (!isset($_POST['id'])) die("No session ID received.");
    if (!isset($_POST['notifications'])) die("No enable/disable notifications argument recieved.");
    $id = htmlspecialchars($_POST['id']);
    $notifications = htmlspecialchars($_POST['notifications']);
    $notifications = ($notifications == 'true') ? true : false;
    
    // Open and check db connection
    require 'link.php';
    if($link == false) die("Disable notifications could not be completed. The server returned the following error message: " . mysqli_connect_error());

    require 'createMail.php'; 

    $stmt = $link->prepare("UPDATE tbl_sessions_v3 SET notifications = ? WHERE id = ?");
    if ( false===$stmt ) die("Disable notifications could not be completed. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("ss", $notifications, $id);
    if ( false===$rc ) die("Disable notifications could not be completed. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Disable notifications could not be completed. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $stmt->close();


    $stmt = $link->prepare("SELECT name, date, title, email FROM tbl_sessions_v3 WHERE id = ?");
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
    } else {
        echo "No session matching ID: ".$id;
    }

    $name = $rows[0]['name'];
    $date = $rows[0]['date'];
    $title = $rows[0]['title'];
    $email = $rows[0]['email'];

    notificationPreferencesMail($name, $date, $title, $email, $id, $notifications);

    echo '{"status":"200","msg":"Notification preferences updated. Notifications are now ' . (($notifications == 'true') ? "enabled" : "disabled") . '."}'; 

    $stmt->close();
    mysqli_close($link);

?>
