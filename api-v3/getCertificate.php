<?php

    //Open and check database connection
    require 'link.php';
    if($link === false) die("Unable to fetch certificate. The server returned the following error message: " . mysqli_connect_error());
    
    require 'functions.php';
    if (!isset($_POST['id'])) die("No session ID received.");
    if (!isset($_POST['certName'])) die("No name for certificate received.");
    if (!isset($_POST['certOrg'])) die("No attendee organisation received.");
    $id = htmlspecialchars($_POST['id']);
    $certName = htmlspecialchars($_POST['certName']);
    $certOrg = htmlspecialchars($_POST['certOrg']);

    $stmt = $link->prepare("INSERT INTO tbl_attendance_v3 (id, name, organisation) VALUES (?, ?, ?)");
    if ( false===$stmt ) die("Attendance data could not be logged. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("sss", $id, $certName, $certOrg);
    if ( false===$rc ) die("Attendance data could not be logged. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Attendance data could not be logged. The server returned the following error message: execute() failed: " . mysqli_error($link));


    $stmt = $link->prepare("SELECT name, date, title, certificate, subsessions FROM tbl_sessions_v3 WHERE id = ?");
    if ( false===$stmt ) die("Certificate could not be retrieved. The server returned the following error message: prepare() failed: " . mysqli_error($link));

    $rc = $stmt->bind_param("s", $id);
    if ( false===$rc ) die("Certificate could not be retrieved. The server returned the following error message: bind_param() failed: " . mysqli_error($link));

    $rc = $stmt->execute();
    if ( false===$rc ) die("Certificate could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));

    $result = $stmt->get_result();
    
    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        $rows = array();
        while($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
        if ($rows[0]['certificate'] == 1) {
            $name = $rows[0]['name'];
            $date = formatDateHuman($rows[0]['date']);
            $title = $rows[0]['title'];
            
            $subsessions = json_decode($rows[0]['subsessions']);
            $subsessionTitles = array();
            $subsessionCount = 0;
            foreach ($subsessions as $sub) {
                $stmtZ = $link->prepare("SELECT title FROM tbl_sessions_v3 WHERE id = ?");
                if ( false===$stmtZ ) die("Subsession title could not be retrieved. The server returned the following error message: prepare() failed: " . mysqli_error($link));
            
                $rcZ = $stmtZ->bind_param("s", $sub);
                if ( false===$rcZ ) die("Subsession title could not be retrieved. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
            
                $rcZ = $stmtZ->execute();
                if ( false===$rcZ ) die("Subsession title could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));
            
                $resultZ = $stmtZ->get_result();
                
                $row_cntZ = $resultZ->num_rows;
                
                if($row_cntZ > 0){
                    $rowsZ = array();
                    while($rZ = mysqli_fetch_assoc($resultZ)) {
                        $rowsZ[] = $rZ;
                        array_push($subsessionTitles, $rowsZ[0]['title']);
                        $subsessionCount ++;
                    }
                }
            }
            
            $subTitleStr = '';
            $i = 0;
            foreach ($subsessionTitles as $subTitle) {
                $i++;
                if ($i == 1) {
                    //do nothing
                } elseif ($i == $subsessionCount){
                    $subTitleStr .= " and ";
                } else {
                    $subTitleStr .= ", ";
                }
                $subTitleStr .= "'".$subTitle."'";
            }
            
            require('FPDF/fpdf.php');
            
            class PDF extends FPDF
            {
            // Page header
                function Header()
                {
                    
                    // Arial bold 15
                    $this->SetFont('Arial','B',30);
                    // Move to the right
                    $this->Cell(15);
                    $this->Image('tick.png',15,5,40);
                    // Title
                    $this->Cell(190,35,'Certificate of attendance',0,0,'C');
                    
                    // Line break
                    $this->Ln(20);
                }
                
                // Page footer
                function Footer()
                {
                    // Position at 1.5 cm from bottom
                    $this->SetY(-15);
                    $this->Image('../logo.png',80,270,50,0,'','https://learnloop.co.uk');
                    // Arial italic 8
                    $this->SetFont('Arial','',10);
                    // Page number
                    $this->Cell(0,10,'Want to collect feedback on your next session? Visit LearnLoop.co.uk',0,0,'C',false,'https://learnloop.co.uk');
                }
            }
            
            // Instanciation of inherited class
            $pdf = new PDF();
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Arial','',20);
            $pdf->ln();
            $pdf->Cell(0,10,'This is to certify that',0,1,'C');
            $pdf->ln();
            $pdf->SetFont('Arial','B',40);
            $pdf->MultiCell(0,14,$certName,0,'C');
            $pdf->ln();
            $pdf->SetFont('Arial','',20);
            $pdf->Cell(0,10,'attended' ,0,1,'C');
            $pdf->ln();
            $pdf->SetFont('Arial','B',40);
            $pdf->MultiCell(0,14,"'" . $title ."'" ,0,'C');
            $pdf->ln();
            if ($subTitleStr) {
                $pdf->SetFont('Arial','',20);
                $pdf->Cell(0,8,'which included the sessions' ,0,1,'C');
                $pdf->SetFont('Arial','B',20);
                $pdf->MultiCell(0,8, $subTitleStr,0,'C');
                $pdf->ln();
                $pdf->SetFont('Arial','',20);
                $pdf->MultiCell(0,8, 'organised by ' . $name,0,'C');
            } else {
                $pdf->SetFont('Arial','',20);
                $pdf->MultiCell(0,8, 'given by ' . $name,0,'C');
            }
            $pdf->ln();
            $pdf->Cell(0,10, 'on ' . $date,0,1,'C');
            $pdf->Output('D','Certificate for '.$title.'.pdf');
            
        } else {
            die("Error retrieving certificate. The server returned the following error message: no certificate for session ID: " . $id);
        }
    } else {
        die("Error retrieving certificate. The server returned the following error message: no session matching ID: " . $id);
    }

    $result->close();
    $stmt->close();
    mysqli_close($link);
?>