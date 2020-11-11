<?php
include_once('../database.php');
include_once('../jwt.php');


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

$db = new database;
$dbConn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

$validationErrors = [];
$isValidationError = false;


$headers = apache_request_headers();

$Auth = $headers['Authorization'];
$Auth = ltrim($Auth,"Bearer");

echo $Auth;exit;