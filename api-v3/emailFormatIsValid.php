<?php

    require 'functions.php';

    $email = $_POST['email'];
    echo '{"emailFormatIsValid":' . emailFormatIsValid($email) . '}';

?>