<?php    
include "qrlib.php";
QRcode::png('https://dev.learnloop.co.uk/'.htmlspecialchars($_REQUEST['id']));
        