<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../authToken.php');
include_once('../post.php');
include_once('../validator.php');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

$db = new database;
$dbConn = $db->connect();

//$data = json_decode(file_get_contents("php://input"));

/* $headers = apache_request_headers();
$Auth = $headers['Authorization'];
$Auth = ltrim($Auth,"Bearer"); */

$validator = new Validator;
$validator->validateRequestType('GET');

$authCheck = new AuthTokenChecker;
$token = $authCheck->getBearerToken();

$uid = $authCheck->validateToken($token);

try 
{
    $post = new Post;
    $post->setCreatedBy($uid);
   try
   {
       $data = $post->getAllPosts();
       $message = $data;
   }
   catch(Exception $e)
   {
       $message = $e->getMessage();
   }
    $response = $response = json_encode(['posts' => $message]);
    echo $response;exit;
}
catch(Exception $e)
{
   echo $e->getmessage();exit;
}
