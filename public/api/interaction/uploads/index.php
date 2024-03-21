<?php

//die('disabled');


function buildID(){
  $permitted_chars = '23456789abcdeghjkmnpqrstuvwxyzABCDEGHJKMNPQRSTUVWXYZ';
  $id = date('Y-m-d').'-'.substr(str_shuffle($permitted_chars), 0, 15);
  return $id;
}


$target_dir = "img/";
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($_FILES["image"]["name"],PATHINFO_EXTENSION));

$res = new stdClass();
$res->error = false;
$res->msg = "";
$res->imageID = buildID() . '.' . $imageFileType;

$target_file = $target_dir . basename($res->imageID);

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["image"]["tmp_name"]);
  if($check !== false) {
    $res->error = false;
    $res->msg .= "File is an image - " . $check["mime"] . ". ";
  } else {
    $res->error = true;
    $res->msg .= "File is not an image. ";
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  $res->error = true;
  $res->msg .= "Sorry, file already exists. ";
}

// Check file size
if ($_FILES["image"]["size"] > 500000) { //500000
  $res->error = true;
  $res->msg .= "Sorry, your file is too large. ";
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  $res->error = true;
  $res->msg .= "Sorry, only JPG, JPEG, PNG & GIF files are allowed. ";
}

// Check if $uploadOk is set to 0 by an error
if (!$res->error) {
  if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
    $res->msg .= "File uploaded: " . $target_file;
  } else {
    $res->error = true;
    $res->msg .= "An unknown error occurred while uploading your file. ";
  }
}

echo json_encode($res);
?>