<?php

    // Open and check connection
    require 'link.php';
    if($link === false) die("Attendance report could not be generated. The server returned the following error message: " . mysqli_connect_error());

    require 'functions.php';
    require 'adminPinHash.php';
    
    if (!isset($_POST['id'])) die("No session ID received.");
    $id = htmlspecialchars($_POST['id']);

    if (!isset($_POST['pin'])) die("No PIN received.");
    $pin = htmlspecialchars($_POST['pin']);
    $pinHash = pinHash($pin);
    if ($pinHash != $adminPinHash) die("Incorrect PIN."); //stop if wrong pin
    
    if (!isset($_POST['format'])) die("No format setting received.");
    $format = htmlspecialchars($_POST['format']);

    unset($sessionData[0]['pinHash']); //remove the pinHash from the array in case this subsequently modified to be returned to client
    
    $stmt = $link->prepare("SELECT name, organisation FROM tbl_attendance_v3 WHERE id = ? ORDER BY organisation, name");
    if ( false===$stmt ) die("Attendance report could not be generated. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) die("Attendance report could not be generated. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Attendance report could not be generated. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $result = $stmt->get_result();
    if ( false===$result ) die("Attendance report could not be generated. The server returned the following error message: get_result() failed: " . mysqli_error($link));

    $row_cnt = $result->num_rows;
    $names = array();
    $organisations = array();
    $mixed = array();
    
    if($row_cnt > 0){
        $sessionData = array();
        while($r = mysqli_fetch_assoc($result)) {
            array_push($names, html_entity_decode($r['name']));
            array_push($organisations, html_entity_decode($r['organisation']));
            array_push($mixed, [html_entity_decode($r['name']), html_entity_decode($r['organisation'])] );
        }
    } else {
        die("No attendance found for session with ID: ".$id);
    }

    $result->close();
    $stmt->close();

    $stmt = $link->prepare("SELECT name, title, date FROM tbl_sessions_v3 WHERE id = ?");
    if ( false===$stmt ) die("Attendance report could not be generated. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) die("Attendance report could not be generated. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Attendance report could not be generated. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $result = $stmt->get_result();
    if ( false===$result ) die("Attendance report could not be generated. The server returned the following error message: get_result() failed: " . mysqli_error($link));

    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        $sessionData = array();
        while($r = mysqli_fetch_assoc($result)) {
            $title = $r['title'];
            $name = $r['name'];
            $date = formatDateHuman($r['date']);
        }
    } else {
        die("No session matching ID: ".$id);
    }

    $result->close();
    $stmt->close();
    
    if ($format == "PDF") {
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
        $pdf->MultiCell(0,10,'Attendance report for "' . $title . '"',0,'C');
        $pdf->SetFont('Arial','',15);
        
        if ($sessionData[0]['subsessions']) {
            $pdf->MultiCell(0,7,'organised by ' . $name . ' on ' . $date,0,'C');
            $pdf->ln();
            $pdf->ln();
        } else {
            $pdf->MultiCell(0,7,'delivered by ' . $name . ' on ' . $date,0,'C');
            $pdf->ln();
            $pdf->ln();
        }
        
        $pdf->SetFont('Arial','',10);
        for($i = 0;$i < count($names);$i++) {
            $pdf->MultiCell(0,5,$names[$i] . " - " . $organisations[$i],0,'');
        }

        $pdf->Output('D','Attendance report for '.$title.'.pdf');
    
    } else {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=attendance-report.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['name', 'organisation']);
        for($i = 0;$i < count($names);$i++) {
            fputcsv($output, $mixed[$i]);
        }
        fclose($output);
    }

    $result->close();
    $stmt->close();
    mysqli_close($link);


?>
