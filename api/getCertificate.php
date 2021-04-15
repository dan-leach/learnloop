<?php

    require 'link.php';
                    
    // Check connection
    if($link === false){
        die("Unable to fetch certificate. The server returned the following error message: " . mysqli_connect_error());
    }
    
    $sIdent = filter_var($_POST['sIdent'], FILTER_SANITIZE_STRING);

    $sql = "SELECT sName, sDate, fName, sCert FROM tbl_sessions WHERE sIdent=?";
    $stmt = $link->prepare($sql);
    if ( false===$stmt ) {
        die("Certificate could not be retrieved. The server returned the following error message: prepare() failed: " . mysqli_error($link));
    }

    $rc = $stmt->bind_param("s", $sIdent);
    if ( false===$rc ) {
        die("Certificate could not be retrieved. The server returned the following error message: bind_param() failed: " . mysqli_error($link));
    }

    $rc = $stmt->execute();
    if ( false===$rc ) {
        die("Certificate could not be retrieved. The server returned the following error message: execute() failed: " . mysqli_error($link));
    }

    $result = $stmt->get_result();
    
    $row_cnt = $result->num_rows;
    if($row_cnt > 0){
        $rows = array();
        while($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
        if ($rows[0]['sCert'] == 1) {
            $sName = $rows[0]['sName'];
            $sDate = $rows[0]['sDate'];
            $fName = $rows[0]['fName'];
            
            $certName = filter_var($_POST['certName'], FILTER_SANITIZE_STRING);
            
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
                    $this->Image('../logo.png',80,270,50);
                    // Arial italic 8
                    $this->SetFont('Arial','',10);
                    // Page number
                    $this->Cell(0,10,'Want to collect feedback on your next session? Visit feedback.danleach.uk',0,0,'C');
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
            $pdf->Cell(0,10, $certName,0,1,'C');
            $pdf->ln();
            $pdf->SetFont('Arial','',20);
            $pdf->Cell(0,10,'attended the session' ,0,1,'C');
            $pdf->ln();
            $pdf->SetFont('Arial','B',40);
            $pdf->Cell(0,10,"'" . $sName ."'" ,0,1,'C');
            $pdf->ln();
            $pdf->SetFont('Arial','',20);
            $pdf->Cell(0,10, 'given by ' . $fName,0,1,'C');
            $pdf->ln();
            $pdf->Cell(0,10, 'on ' . $sDate,0,1,'C');
            $pdf->Output('D',$sName.'-certificate.pdf');
            
        } else {
            echo "Error retrieving certificate. The server returned the following error message: no certificate for session ID: " . $sIdent;
        }
    } else {
        echo "Error retrieving certificate. The server returned the following error message: no session matching ID: " . $sIdent;
    }

    $result->close();
    $stmt->close();
    mysqli_close($link);


?>