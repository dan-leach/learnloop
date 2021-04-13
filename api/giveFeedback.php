<?php

    require 'link.php';
                    
    // Check connection
    if($link === false){
        die("Session data could not be logged. The server returned the following error message: " . mysqli_connect_error());
    }

    $sIdent = filter_var($_POST['sIdent'], FILTER_SANITIZE_STRING);
    $positiveComments = filter_var($_POST['positiveComments'], FILTER_SANITIZE_STRING);
    $constructiveComments = filter_var($_POST['constructiveComments'], FILTER_SANITIZE_STRING);
    $overallScore = filter_var($_POST['overallScore'], FILTER_SANITIZE_NUMBER_INT);

    // Attempt insert query execution
    $sql = "INSERT INTO tbl_feedback_v2 (ID, sIdent, positiveComments, constructiveComments, overallScore) VALUES (null, '$sIdent', '$positiveComments', '$constructiveComments', '$overallScore')";
    if(mysqli_query($link, $sql)){
        //insert successful
    } else {
        die("Session data could not be logged. The server returned the following error message: " . mysqli_error($link));
    }

    //initialize variables outside scope of sql query function
    $sName = '';
    $sDate = '';
    $fName = '';
    $fEmail = '';

    // Get session details required for sending email
    if ($result = $link->query("SELECT sName, sDate, fName, fEmail FROM tbl_sessions WHERE sIdent = '$sIdent'")) {
        $row_cnt = $result->num_rows;
        if($row_cnt > 0){
            $rows = array();
            while($r = mysqli_fetch_assoc($result)) {
                $rows[] = $r;
                $sName = $r['sName'];
                $sDate = $r['sDate'];
                $fName = $r['fName'];
                $fEmail = $r['fEmail'];
            }
        } else {
            die("Your feedback was logged successfully but there was a problem sending it to the session facilitator as the session details required to send the message could not be retrieved. The server returned the following error: 'session ID not found'");
        }
        /* close result set */
        $result->close();
    } else {
        die("Your feedback was logged successfully but there was a problem sending it to the session facilitator as the session details required to send the message could not be retrieved. The server returned the following error: ") . mysqli_error($link);
    }

    // Close connection
    mysqli_close($link);

    //send email
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $messageContent = "
        <html>
            <h1>Some feedback has been submitted for your session.</h1>
            Hello " . $fName . ",<br>
            <br>
            A attendee of the session '" . $sName . "' that you delivered on " . $sDate . " has provided feedback for you.<br>
            <br>
            <strong>Positive comments:</strong><br>" . 
            $positiveComments . "<br>
            <br>
            <strong>Constructive comments:</strong><br>" . 
            $constructiveComments . "<br>
            <br>
            <strong>Overall score:</strong> " . $overallScore . "<br>
            <br>
            Kind regards,<br>
            Feedback Tool<br>
            <a href='feedback.danleach.uk'>feedback.danleach.uk</a>
        </html>";

    /* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
    $mail = new PHPMailer(TRUE);

    /* Open the try/catch block. */
    try {
    /* Set the mail sender. */
    $mail->setFrom('feedback@danleach.uk', 'Feedback Tool');

    /* Add a recipient. */
    $mail->addAddress($fEmail, $fName);

    /* Set the subject. */
    $mail->Subject = 'Feedback Tool Submission';

    $mail->isHTML(TRUE);
    /* Set the mail message body. */
    $mail->Body = $messageContent;

    /* Finally send the mail. */
    $mail->send();

    echo '{"status":"200","msg":"feedback insert into db successful; email to facilitator successful"}';
    }
    catch (Exception $e)
    {
    /* PHPMailer exception. */
    echo "Your feedback was logged successfully, but there was a problem sending it to the session facilitator. The server returned the following error message: ";
    echo $e->errorMessage();
    }
    catch (\Exception $e)
    {
    /* PHP exception (note the backslash to select the global namespace Exception class). */
    echo $e->getMessage();
    }
    
?>