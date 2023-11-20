<?php

function createPin(){ // Generate pin
    $permitted_chars = '0123456789';
    return substr(str_shuffle($permitted_chars), 0, 6);
}

function hashPin($pin){ //returns the hash of a pin
    return hash("sha256", $pin);
}

function pinIsValid($pin, $pinHash){ //check a pin matches pinHash
    require 'private/keys.php';
    $hash = hashPin($pin);
    if ($adminPinHash == $hash) return true;
    return ($pinHash == $hash);
}

function buildID($module){
    $permitted_chars = '23456789abcdeghjkmnpqrstuvwxyzABCDEGHJKMNPQRSTUVWXYZ';
    $id = '';
    if ($module == 'feedback') {
        $id = 'f';
    } else if ($module == 'interact') {
        $id = 'i';
    } else {
        send_error_response("Module [".$module."] not recognised at buildID", 400);
    }
    $id .= substr(str_shuffle($permitted_chars), 0, 5);
    return $id;
}

function createUniqueID($link, $module){ // Generate unique short ID
    do {
        $pass = 0;
        $id = buildID($module);
        if (dbSessionExists($id, $link)) {
            $id = buildID($module);
        } else {
            $pass ++;
        }
    } while ($pass < 1);
    return $id;
}

function validateDate($date, $format = 'Y-m-d H:i:s') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function formatDateHuman($date){
    $d = date_create($date);
    return date_format($d, 'd/m/Y');
}

function organiseQuestionFeedback($questions, $questionFeedback){

    foreach ($questions as $question) {
        $question->responses = array();
        foreach($question->options as $option) $option->count = 0;
    }
    
    foreach ($questionFeedback as $responseSet) { 
        foreach ($responseSet as $response) {
            foreach ($questions as $question){
                if ($response->title == $question->title) {
                    if ($question->type == 'text') {
                        array_push($question->responses, $response->response);
                    } else {
                        foreach($question->options as $option) {
                            if ($question->type == 'select') if ($response->response == $option->title) $option->count ++;
                            if ($question->type == 'checkbox') {
                                foreach($response->options as $rOption) {
                                    if ($rOption->title == $option->title && $rOption->selected) $option->count ++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $questions;
}

function outputFeedbackPDF($feedback){
    require('FPDF/fpdf.php');
            
    class PDFa extends FPDF {
        // Page footer
        function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            $this->Image('shared/logo.png',80,270,50,0,'','https://learnloop.co.uk' );
            // Arial italic 8
            $this->SetFont('Arial','',10);
            // Page number
            $this->Cell(0,5,'Want to collect feedback on your next session? Visit LearnLoop.co.uk',0,0,'C',false,'https://learnloop.co.uk');
            $this->SetFont('Arial','I',10);
            $this->Cell(0,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');
        }
    }

    // Instanciation of inherited class
    $pdf = new PDFa();
    $pdf->SetAutoPageBreak(true,25);
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetFont('Arial','B',20);
    $pdf->ln();
    $pdf->MultiCell(0,10,'Feedback report for "' . $feedback['title'] . '"',0,'C');
    $pdf->SetFont('Arial','',15);

    if ($feedback['subsessions']) {
        $pdf->MultiCell(0,7,'organised by ' . $feedback['name'] . ' on ' . $feedback['date'],0,'C');
        $pdf->ln();
        $pdf->ln();
        $pdf->SetFont('Arial','',20);
        $pdf->MultiCell(0,10,'Feedback for organiser',0,'C');
    } else {
        $pdf->MultiCell(0,7,'delivered by ' . $feedback['name'] . ' on ' . $feedback['date'],0,'C');
        $pdf->ln();
        $pdf->ln();
    }

    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,7,'Positive comments:',0,'');
    $pdf->SetFont('Arial','',10);
    foreach ($feedback['positive'] as $pos) {
        $pdf->MultiCell(0,4,$pos,0,'');
    }

    $pdf->ln();
    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,7,'Constructive comments:',0,'');
    $pdf->SetFont('Arial','',10);
    foreach ($feedback['negative'] as $neg) {
        $pdf->MultiCell(0,4,$neg,0,'');
    }

    if ($feedback['questions']){
        foreach ($feedback['questions'] as $question) {
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,7,$question->title,0,'');
            $pdf->SetFont('Arial','',10);
            if ($question->type == 'text') {
                foreach ($question->responses as $response) $pdf->MultiCell(0,4,$response,0,'');
            } else {
                foreach ($question->options as $option) $pdf->MultiCell(0,4,$option->title . ": " . $option->count,0,'');
            }
        }
    }

    $pdf->ln();
    $pdf->SetFont('Arial','',12);
    $pdf->MultiCell(0,7,'Overall score: ' . round((array_sum($feedback['score'])/count($feedback['score'])),1) . '/100',0,'');

    if ($feedback['subsessions']) {
        $pdf->ln();
        $pdf->ln();
        $pdf->SetFont('Arial','',20);
        $pdf->MultiCell(0,10,'Feedback for sessions',0,'C');
        
        foreach ($feedback['subsessions'] as $subJSON) {
            $sub = json_decode($subJSON);
            $pdf->SetFont('Arial','B',12);
            $pdf->MultiCell(0,7,'"' . $sub->title . '" by ' . $sub->name,0,'');
            
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,7,'Positive comments:',0,'');
            $pdf->SetFont('Arial','',10);
            foreach ($sub->positive as $subPos) {
                $pdf->MultiCell(0,4,$subPos,0,'');
            }
            
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,7,'Constructive comments:',0,'');
            $pdf->SetFont('Arial','',10);
            foreach ($sub->negative as $subNeg) {
                $pdf->MultiCell(0,4,$subNeg,0,'');
            }
            
            $pdf->ln();
            $pdf->SetFont('Arial','',12);
            $pdf->MultiCell(0,7,'Overall score: ' . round((array_sum($sub->score)/count($sub->score)),1) . '/100',0,'');

            $pdf->ln();
        }
    }


    $pdf->Output('D','Feedback report for '.$feedback['title'].'.pdf');
}

function buildAttendeesByOrg($names, $organisations){
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

    return $attendeesByOrg;
}

function countAttendees($attendees){
    $count = 0;
    foreach ($attendees as $org) $count += count($org['attendees']);
    return $count;
}

function outputAttendancePDF($attendance){
    require('FPDF/fpdf.php');
                
    class PDFb extends FPDF {
        // Page footer
        function Footer() {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            $this->Image('shared/logo.png',80,270,50,0,'','https://learnloop.co.uk' );
            // Arial italic 8
            $this->SetFont('Arial','',10);
            // Page number
            $this->Cell(0,5,'Want to collect feedback on your next session? Visit LearnLoop.co.uk',0,0,'C',false,'https://learnloop.co.uk');
            $this->SetFont('Arial','I',10);
            $this->Cell(0,5,'Page '.$this->PageNo().' of {nb}',0,0,'R');
        }
    }

    // Instanciation of inherited class
    $pdf = new PDFb();
    $pdf->SetAutoPageBreak(true,25);
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetFont('Arial','B',20);
    $pdf->ln();
    $pdf->MultiCell(0,10,'Attendance report for "' . $attendance['title'] . '"',0,'C');
    $pdf->SetFont('Arial','',15);
    
    if ($attendance['subsessions']) {
        $pdf->MultiCell(0,7,'organised by ' . $attendance['name'] . ' on ' . $attendance['date'],0,'C');
        $pdf->ln();
        $pdf->ln();
    } else {
        $pdf->MultiCell(0,7,'delivered by ' . $attendance['name'] . ' on ' . $attendance['date'],0,'C');
        $pdf->ln();
        $pdf->ln();
    }

    $pdf->SetFont('Arial','B',12);
    $pdf->MultiCell(0,5,"Total attendees: " . $attendance['attendeeCount'],0,'');
    $pdf->ln();

    for($i = 0;$i < count($attendance['attendees']);$i++) {
        $pdf->SetFont('Arial','',12);
        $pdf->MultiCell(0,7,$attendance['attendees'][$i]['name'] . ": " . $attendance['attendees'][$i]['count'] . " attendee(s)",0,'');
        $attendees = '';
        for($x = 0;$x < count($attendance['attendees'][$i]['attendees']);$x++) {
            if ($x > 0) $attendees .= ", ";
            $attendees .= $attendance['attendees'][$i]['attendees'][$x];
        }
        $pdf->SetFont('Arial','',10);
        $pdf->MultiCell(0,5,$attendees,0,'');
        $pdf->ln();
    }
    
    $pdf->Output('D','Attendance report for '.$attendance['title'].'.pdf');
}

function outputAttendanceCSV($attendance){
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=attendance-report.csv');

    $output = fopen('php://output', 'w');

    $max = 0;
    for($i = 0;$i < count($attendance['attendees']);$i++) {
        if (count($attendance['attendees'][$i]['attendees']) > $max) $max = count($attendance['attendees'][$i]['attendees']);
    }

    $orgs = array();
    foreach ($attendance['attendees'] as $org) array_push($orgs, $org['name']);

    fputcsv($output, $orgs);
    $row = array();
    for($x = 0;$x < $max;$x++) {
        $row = [];
        for($i = 0;$i < count($orgs);$i++) {
            array_push($row, $attendance['attendees'][$i]['attendees'][$x]);
        }
        fputcsv($output, $row);
    }

    fclose($output);
}

function outputCertificate($details, $name){
    $stringOfSubsessionTitles = buildStringOfSubsessionTitles($details);
    
    require('FPDF/fpdf.php');

    class PDFc extends FPDF
        {
        // Page header
            function Header()
            {
                
                // Arial bold 15
                $this->SetFont('Arial','B',30);
                // Move to the right
                $this->Cell(15);
                $this->Image('shared/tick.png',15,5,40);
                // Title
                $this->Cell(190,35,'Certificate of attendance',0,0,'C');
            }
            
            // Page footer
            function Footer()
            {
                // Position at 1.5 cm from bottom
                $this->SetY(-15);
                $this->Image('shared/logo.png',80,270,50,0,'','https://learnloop.co.uk');
                // Arial italic 8
                $this->SetFont('Arial','',10);
                // Page number
                $this->Cell(0,10,'Want to collect feedback on your next session? Visit LearnLoop.co.uk',0,0,'C',false,'https://learnloop.co.uk');
            }
        }
        
        // Instanciation of inherited class
        $pdf = new PDFc();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial','',20);
        // Line break
        if (strlen($stringOfSubsessionTitles) < 300) {
            $pdf->Ln(20);
        }
        $pdf->ln();
        $pdf->Cell(0,10,'This is to certify that',0,1,'C');
        $pdf->ln();
        $pdf->SetFont('Arial','B',40);
        $pdf->MultiCell(0,14,$name,0,'C');
        $pdf->ln();
        $pdf->SetFont('Arial','',20);
        $pdf->Cell(0,10,'attended' ,0,1,'C');
        $pdf->ln();
        $pdf->SetFont('Arial','B',40);
        $pdf->MultiCell(0,14,"'" . $details['title'] ."'" ,0,'C');
        $pdf->ln();
        if ($stringOfSubsessionTitles) {
            $pdf->SetFont('Arial','',20);
            $pdf->Cell(0,8,'which included the sessions' ,0,1,'C');
            if (strlen($stringOfSubsessionTitles) < 300) {
                $pdf->SetFont('Arial','B',20);
            } else {
                $pdf->SetFont('Arial','B',10);
            }
            $pdf->MultiCell(0,8, $stringOfSubsessionTitles,0,'C');
            $pdf->ln();
            $pdf->SetFont('Arial','',20);
            $pdf->MultiCell(0,8, 'organised by ' . $details['name'],0,'C');
        } else {
            $pdf->SetFont('Arial','',20);
            $pdf->MultiCell(0,8, 'given by ' . $details['name'],0,'C');
        }
        $pdf->ln();
        $pdf->Cell(0,10, 'on ' . $details['date'],0,1,'C');
        $pdf->Output('D','Certificate for '.$details['title'].'.pdf');
}

function buildStringOfSubsessionTitles($details){
    $res = '';
    foreach ($details['subsessions'] as $subsession){
        $res .= $subsession['title'];
        $res .= ", ";
    }
    return $res;
}

?>