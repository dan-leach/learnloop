<?php

    // Open and check connection
    require 'link.php';
    if($link === false) die("Feedback report could not be generated. The server returned the following error message: " . mysqli_connect_error());

    require 'functions.php';
    require 'adminPinHash.php';
    
    if (!isset($_POST['id'])) die("No session ID received.");
    $id = htmlspecialchars($_POST['id']);
    if (!isset($_POST['pin'])) die("No PIN received.");
    $pin = htmlspecialchars($_POST['pin']);
    $pinHash = pinHash($pin);
    if (isset($_POST['subID'])) $subID = htmlspecialchars($_POST['subID']);

    $checkPIN = true;
    if ($subID) {
        $stmt = $link->prepare("SELECT subsessions, pinHash FROM tbl_sessions_v3 WHERE id = ?");
        if ( false===$stmt ) die("Feedback report could not be generated. The server returned the following error message: prepare() failed: " . mysqli_error($link));

        $rc = $stmt->bind_param("s",$id);
        if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

        $rc = $stmt->execute();
        if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: execute() failed: " . mysqli_error($link));

        $result = $stmt->get_result();
        if ( false===$result ) die("Feedback report could not be generated. The server returned the following error message: get_result() failed: " . mysqli_error($link));

        $row_cnt = $result->num_rows;
        if($row_cnt > 0){
            $sessionData = array();
            while($r = mysqli_fetch_assoc($result)) {
                $seriesData[] = $r;
            }
            
            if ($pinHash != $adminPinHash && $pinHash != $seriesData[0]['pinHash']) die("Incorrect PIN."); //stop if wrong pin
            if (!str_contains($seriesData[0]['subsessions'],$subID)) die("Subsession ID (" . $subID . ") not part of series with ID: " . $id . "."); //stop if subsession not part of this series
            $id = $subID;
            $checkPIN = false;
        } else {
            die("No session matching ID: ".$id);
        }
        
    }
    
    $stmt = $link->prepare("SELECT name, title, date, questions, subsessions, pinHash FROM tbl_sessions_v3 WHERE id = ?");
    if ( false===$stmt ) die("Feedback report could not be generated. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $result = $stmt->get_result();
    if ( false===$result ) die("Feedback report could not be generated. The server returned the following error message: get_result() failed: " . mysqli_error($link));

    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        $sessionData = array();
        while($r = mysqli_fetch_assoc($result)) {
            $sessionData[] = $r;
        }
        $sessionData[0]['title'] = html_entity_decode($sessionData[0]['title']);
        $sessionData[0]['name'] = html_entity_decode($sessionData[0]['name']);
        $sessionData[0]['date'] = formatDateHuman($sessionData[0]['date']);
        $questions = json_decode($sessionData[0]['questions']);
    } else {
        die("No session matching ID: ".$id);
    }
    
    if ($pinHash != $adminPinHash and $pinHash != $sessionData[0]['pinHash'] and $checkPIN) die("Incorrect PIN."); //stop if wrong pin
    unset($sessionData[0]['pinHash']); //remove the pinHash from the array to be returned to client

    $sessionData[0]['subsessionIDs'] = json_decode($sessionData[0]['subsessions']); //convert subsession IDs stored as json back into array
    unset($sessionData[0]['subsessions']); //clear the subsession object ready to be repopulated with feedback data
    
    $stmt = $link->prepare("SELECT positive, negative, questions, score FROM tbl_feedback_v3 WHERE id = ?");
    if ( false===$stmt ) die("Feedback report could not be generated. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $result = $stmt->get_result();
    if ( false===$result ) die("Feedback report could not be generated. The server returned the following error message: get_result() failed: " . mysqli_error($link));

    $row_cnt = $result->num_rows;
    $sessionData[0]['positive'] = array();
    $sessionData[0]['negative'] = array();
    $questionFeedback = array();
    $sessionData[0]['score'] = array();
    if($row_cnt > 0){
        while($r = mysqli_fetch_assoc($result)) {
            array_push($sessionData[0]['positive'], html_entity_decode($r['positive']));
            array_push($sessionData[0]['negative'], html_entity_decode($r['negative']));
            array_push($questionFeedback, json_decode($r['questions']));
            array_push($sessionData[0]['score'], html_entity_decode($r['score']));
        }
    } else {
        array_push($sessionData[0]['positive'], "No feedback found.");
        array_push($sessionData[0]['negative'], "No feedback found.");
        array_push($sessionData[0]['questionFeedback'], "No feedback found.");
        array_push($sessionData[0]['score'], "No feedback found.");
    }

    if ($sessionData[0]['subsessionIDs']) {
        foreach ($sessionData[0]['subsessionIDs'] as $key=>$subID) {
    
            $stmt = $link->prepare("SELECT name, title FROM tbl_sessions_v3 WHERE id = ?");
            if ( false===$stmt ) die("Feedback report could not be generated. The server returned the following error message: prepare() failed: " . mysqli_error($link));
        
            $rc = $stmt->bind_param("s",$subID);
            if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
        
            $rc = $stmt->execute();
            if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: execute() failed: " . mysqli_error($link));
        
            $result = $stmt->get_result();
            if ( false===$result ) die("Feedback report could not be generated. The server returned the following error message: get_result() failed: " . mysqli_error($link));
        
            $row_cnt = $result->num_rows;
            if($row_cnt > 0){
                while($r = mysqli_fetch_assoc($result)) {
                    $sessionData[0]['subsessions'][$key]['name'] = html_entity_decode($r['name']);
                    $sessionData[0]['subsessions'][$key]['title'] = html_entity_decode($r['title']);
                }
            }
    
            $sessionData[0]['subsessions'][$key]['id'] = $subID;
            $stmt = $link->prepare("SELECT positive, negative, score FROM tbl_feedback_v3 WHERE id = ?");
            if ( false===$stmt ) die("Feedback report could not be generated. The server returned the following error message: prepare() failed: " . mysqli_error($link));
    
            $rc = $stmt->bind_param("s",$subID);
            if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
    
            $rc = $stmt->execute();
            if ( false===$rc ) die("Feedback report could not be generated. The server returned the following error message: execute() failed: " . mysqli_error($link));
    
            $result = $stmt->get_result();
            if ( false===$result ) die("Feedback report could not be generated. The server returned the following error message: get_result() failed: " . mysqli_error($link));
    
            $row_cnt = $result->num_rows;
            $sessionData[0]['subsessions'][$key]['positive'] = array();
            $sessionData[0]['subsessions'][$key]['negative'] = array();
            $sessionData[0]['subsessions'][$key]['score'] = array();
            if($row_cnt > 0){
                while($r = mysqli_fetch_assoc($result)) {
                    array_push($sessionData[0]['subsessions'][$key]['positive'], html_entity_decode($r['positive']));
                    array_push($sessionData[0]['subsessions'][$key]['negative'], html_entity_decode($r['negative']));
                    array_push($sessionData[0]['subsessions'][$key]['score'], html_entity_decode($r['score']));
                }
            } else {
                array_push($sessionData[0]['subsessions'][$key]['positive'], "No feedback found.");
                array_push($sessionData[0]['subsessions'][$key]['negative'], "No feedback found.");
                array_push($sessionData[0]['subsessions'][$key]['score'], "No feedback found.");
            }
        }
    }
    
    unset($sessionData[0]['subsessionIDs']); //clear the subsessionIDs once no longer required

    foreach ($questions as $key=>$question) {
        $question->responses = array();
        foreach ($questionFeedback as $key=>$feedback) {
            foreach ($feedback as $key=>$item) {
                if ($item->title == $question->title) {
                    array_push($question->responses, $item->response);
                }
            }
        }
        if ($question->type != 'text') {
            foreach($question->options as $key=>$option) {
                $option->count = 0;
                foreach($question->responses as $key=>$response) {
                    if ($question->type == 'checkbox') if (str_contains($response, $option->title)) $option->count ++;
                    if ($question->type == 'select') if ($response == $option->title) $option->count ++;
                }
            }
        }
    }

    $sessionData[0]['questions'] = $questions;

    require('FPDF/fpdf.php');
            
    class PDF extends FPDF {
        // Page footer
        function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            $this->Image('../logo.png',80,270,50,0,'','https://learnloop.co.uk' );
            // Arial italic 8
            $this->SetFont('Arial','',10);
            // Page number
            $this->Cell(0,5,'Want to collect feedback on your next session? Visit LearnLoop.co.uk',0,0,'C',false,'https://learnloop.co.uk');
            $this->SetFont('Arial','I',10);
            $this->Cell(0,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');
        }
    }

    // Instanciation of inherited class
    $pdf = new PDF();
    $pdf->SetAutoPageBreak(true,25);
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetFont('Arial','B',20);
    $pdf->ln();
    $pdf->MultiCell(0,10,'Feedback report for "' . $sessionData[0]['title'] . '"',0,'C');
    $pdf->SetFont('Arial','',15);
    
    if ($sessionData[0]['subsessions']) {
        $pdf->MultiCell(0,7,'organised by ' . $sessionData[0]['name'] . ' on ' . $sessionData[0]['date'],0,'C');
        $pdf->ln();
        $pdf->ln();
        $pdf->SetFont('Arial','',20);
        $pdf->MultiCell(0,10,'Feedback for organiser',0,'C');
    } else {
        $pdf->MultiCell(0,7,'delivered by ' . $sessionData[0]['name'] . ' on ' . $sessionData[0]['date'],0,'C');
        $pdf->ln();
        $pdf->ln();
    }

    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,7,'Positive comments:',0,'');
    $pdf->SetFont('Arial','',10);
    foreach ($sessionData[0]['positive'] as $pos) {
        $pdf->MultiCell(0,4,$pos,0,'');
    }

    $pdf->ln();
    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,7,'Constructive comments:',0,'');
    $pdf->SetFont('Arial','',10);
    foreach ($sessionData[0]['negative'] as $neg) {
        $pdf->MultiCell(0,4,$neg,0,'');
    }

    foreach ($questions as $key=>$question) {
        $pdf->ln();
        $pdf->SetFont('Arial','',12);
        $pdf->MultiCell(0,7,$question->title,0,'');
        $pdf->SetFont('Arial','',10);
        if ($question->type == 'text') {
            foreach ($question->responses as $key=>$response) $pdf->MultiCell(0,4,$response,0,'');
        } else {
            foreach ($question->options as $key=>$option) $pdf->MultiCell(0,4,$option->title . ": " . $option->count,0,'');
        }
    }

    $pdf->ln();
    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,7,'Overall score: ' . round((array_sum($sessionData[0]['score'])/count($sessionData[0]['score'])),1) . '/100',0,'');

    if ($sessionData[0]['subsessions']) {
        $pdf->ln();
        $pdf->ln();
        $pdf->SetFont('Arial','',20);
        $pdf->MultiCell(0,10,'Feedback for sessions',0,'C');
        
        foreach ($sessionData[0]['subsessions'] as $sub) {
            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,'"' . $sub['title'] . '" by ' . $sub['name'],0,'');
            
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,7,'Positive comments:',0,'');
            $pdf->SetFont('Arial','',10);
            foreach ($sub['positive'] as $subPos) {
                $pdf->MultiCell(0,4,$subPos,0,'');
            }
            
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,7,'Constructive comments:',0,'');
            $pdf->SetFont('Arial','',10);
            foreach ($sub['negative'] as $subNeg) {
                $pdf->MultiCell(0,4,$subNeg,0,'');
            }
            
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,7,'Overall score: ' . round((array_sum($sub['score'])/count($sub['score'])),1) . '/100',0,'');

            $pdf->ln();
        }
    }


    $pdf->Output('D','Feedback report for '.$sessionData[0]['title'].'.pdf');

    $result->close();
    $stmt->close();
    mysqli_close($link);


?>