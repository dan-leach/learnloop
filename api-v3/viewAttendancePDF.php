<?php

    // Open and check connection
    require 'link.php';
    if($link === false) die("Attendance report could not be generated. The server returned the following error message: " . mysqli_connect_error());

    require 'functions.php';
        
    if (!isset($_POST['id'])) die("No session ID received.");
    $id = htmlspecialchars($_POST['id']);

    if (!isset($_POST['pin'])) die("No PIN received.");
    $pin = trim(htmlspecialchars($_POST['pin']));
    $pinHash = pinHash($pin);

    if (!isset($_POST['format'])) die("No format setting received.");
    $format = htmlspecialchars($_POST['format']);

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
    
    if($row_cnt > 0){
        while($r = mysqli_fetch_assoc($result)) {
            array_push($names, html_entity_decode($r['name']));
            array_push($organisations, html_entity_decode($r['organisation']));
        }
    } else {
        die("No attendance found for session with ID: ".$id);
    }

    $result->close();
    $stmt->close();

    $stmt = $link->prepare("SELECT name, title, date, subsessions, pinHash FROM tbl_sessions_v3 WHERE id = ?");
    if ( false===$stmt ) die("Attendance report could not be generated. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("s",$id);
    if ( false===$rc ) die("Attendance report could not be generated. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Attendance report could not be generated. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $result = $stmt->get_result();
    if ( false===$result ) die("Attendance report could not be generated. The server returned the following error message: get_result() failed: " . mysqli_error($link));

    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        while($r = mysqli_fetch_assoc($result)) {
            $title = $r['title'];
            $name = $r['name'];
            $date = formatDateHuman($r['date']);
            $subsessions = $r['subsessions'];
            $sessionPinHash = $r['pinHash'];
        }
    } else {
        die("No session matching ID: ".$id);
    }
    
    require 'adminPinHash.php';
    if ($pinHash != $adminPinHash and $pinHash != $sessionPinHash) die("Incorrect PIN."); //stop if wrong pin

    $result->close();
    $stmt->close();

    //build array of unique organisations
    $uniqueOrgs = array();
    for($i = 0;$i < count($organisations);$i++) {
        if (!in_array($organisations[$i], $uniqueOrgs)) {
            array_push($uniqueOrgs, $organisations[$i]);
        }
    }
    
    //build array of unique names with organisations
    $uniqueAttendees = array();
    $uniqueNames = array();
    for($i = 0;$i < count($names);$i++) {
        if (!in_array($names[$i], $uniqueNames)) {
            array_push($uniqueNames, $names[$i]);
            array_push($uniqueAttendees, [trim($names[$i]),$organisations[$i]]);
        }
    }

    //count unique names
    $totalAttendees = count($uniqueAttendees);

    if ($totalAttendees < 3) die("Cannot generate attendance report when less than 3 people have submitted feedback. This is to prevent attendees being linked to their feedback.");

    $attendeesByOrg = array();
    for($i = 0;$i < count($uniqueOrgs);$i++) {
        $uOrg['name'] = $uniqueOrgs[$i];
        $count = 0;
        $uOrg['attendees'] = array();
        for($x = 0;$x < count($uniqueAttendees);$x++) {
            if ($uniqueAttendees[$x][1] == $uOrg['name']) {
                $count++;
                array_push($uOrg['attendees'],$uniqueAttendees[$x][0]);
            }
        }
        $uOrg['count'] = $count;
        array_push($attendeesByOrg, $uOrg);
    }
    
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
        
        if ($subsessions) {
            $pdf->MultiCell(0,7,'organised by ' . $name . ' on ' . $date,0,'C');
            $pdf->ln();
            $pdf->ln();
        } else {
            $pdf->MultiCell(0,7,'delivered by ' . $name . ' on ' . $date,0,'C');
            $pdf->ln();
            $pdf->ln();
        }

        $pdf->SetFont('Arial','B',12);
        $pdf->MultiCell(0,5,"Total attendees: " . $totalAttendees,0,'');
        $pdf->ln();

        for($i = 0;$i < count($attendeesByOrg);$i++) {
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,7,$attendeesByOrg[$i]['name'] . ": " . $attendeesByOrg[$i]['count'] . " attendee(s)",0,'');
            $attendees = '';
            for($x = 0;$x < count($attendeesByOrg[$i]['attendees']);$x++) {
                if ($x > 0) $attendees .= ", ";
                $attendees .= $attendeesByOrg[$i]['attendees'][$x];
            }
            $pdf->SetFont('Arial','',10);
            $pdf->MultiCell(0,5,$attendees,0,'');
            $pdf->ln();
        }
        
        $pdf->Output('D','Attendance report for '.$title.'.pdf');
    
    } else {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=attendance-report.csv');

        $output = fopen('php://output', 'w');

        $max = 0;
        for($i = 0;$i < count($attendeesByOrg);$i++) {
            if (count($attendeesByOrg[$i]['attendees']) > $max) $max = count($attendeesByOrg[$i]['attendees']);
        }

        fputcsv($output, $uniqueOrgs);
        $row = array();
        for($x = 0;$x < $max;$x++) {
            $row = [];
            for($i = 0;$i < count($uniqueOrgs);$i++) {
                array_push($row, $attendeesByOrg[$i]['attendees'][$x]);
            }
            fputcsv($output, $row);
        }

        fclose($output);
    }

    mysqli_close($link);

?>
