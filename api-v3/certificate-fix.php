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

    $name ='Sarah Cheney';
    $date = '30/11/2022';
    $title = "Diabetes, Risk and Duty of Candour (ST4-8 Regional Teaching Day)";
            
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
                    $this->Cell(0,10,'Want to collect feedback on your next session? Visit LearnLoop.co.uk',0,0,'C');
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
            $pdf->Cell(0,10,'attended the session' ,0,1,'C');
            $pdf->ln();
            $pdf->SetFont('Arial','B',40);
            $pdf->MultiCell(0,14,"'" . $title ."'" ,0,'C');
            $pdf->ln();
            $pdf->SetFont('Arial','',20);
            $pdf->MultiCell(0,8, 'given by ' . $name,0,'C');
            $pdf->ln();
            $pdf->Cell(0,10, 'on ' . $date,0,1,'C');
            $pdf->Output('D',$title.'-certificate.pdf');
        
?>