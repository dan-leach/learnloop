<?php

    require 'link.php';
                    
    // Check connection
    if($link === false){
        die("Session data could not be logged. The server returned the following error message: " . mysqli_connect_error());
    }

    $sIdent = "'".$_POST['sIdent']."'";
    $positiveComments = "'".$_POST['positiveComments']."'";
    $constructiveComments = "'".$_POST['constructiveComments']."'";
    $overallScore = "'".$_POST['overallScore']."'";

    // Attempt insert query execution
    $sql = "INSERT INTO tbl_feedback_v2 (ID, sIdent, positiveComments, constructiveComments, overallScore) VALUES (null, $sIdent, $positiveComments, $constructiveComments, $overallScore)";
    if(mysqli_query($link, $sql)){
        echo '{"status":"200","msg":"feedback insert into db successful"}';
    } else {
        echo "Session data could not be logged. The server returned the following error message: " . mysqli_error($link);
    }
        
    // Close connection
    mysqli_close($link);


?>