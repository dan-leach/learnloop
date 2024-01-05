<?php

require 'shared/mail.php';
require 'shared/utilities.php';
require 'private/keys.php';
require 'private/betaTesters.php';

function handle_error($error)
{
    //sendMail(addHeader().$error."<br><br>".addFooter(false), 'LearnLoop error notification', 'mail@learnloop.co.uk', 'LearnLoop');
    http_response_code(500);
    die(json_encode('Sorry, an unexpected error has occurred. This event has been logged. If you keep seeing this message please contact mail@learnloop.co.uk including the error message below and a description of what you were doing when it appeared. ' . preg_replace('/[[:cntrl:]]/', '', $error)));
}
set_exception_handler('handle_error');

function send_error_response($msg, $status)
{
    http_response_code($status);
    die(json_encode($msg));
}

if (!isset($_POST['module'])) send_error_response("Module must be defined", 400);
$module = htmlspecialchars($_POST['module']);
if (!isset($_POST['route'])) send_error_response("Route must be defined", 400);
$route = htmlspecialchars($_POST['route']);

//not all routes require all of the variables below - these are checked at the router level
if (isset($_POST['id'])) $id = htmlspecialchars($_POST['id']);
if (isset($_POST['pin'])) $pin = htmlspecialchars($_POST['pin']);
if (isset($_POST['data'])) $data = json_decode($_POST['data']); //data must be sanitised later

if ($module == 'feedback') {
    require 'feedback/index.php';
} elseif ($module == 'interaction') {
    require 'interaction/index.php';
} else {
    send_error_response("[" . $module . "] is not a valid module", 400);
}
