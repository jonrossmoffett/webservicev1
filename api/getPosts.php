<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../authToken.php');
include_once('../post.php');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

$db = new database;
$dbConn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

$validationErrors = [];
$isValidationError = false;

/* $headers = apache_request_headers();
$Auth = $headers['Authorization'];
$Auth = ltrim($Auth,"Bearer"); */


$authCheck = new AuthTokenChecker;
$token = $authCheck->getBearerToken();
echo $token;
$authCheck->validateToken($token);

try 
{
    $payload = JWT::decode($token,SECRETE_KEY,['HS256']);
    $post = new Post;
    $post->setCreatedBy($payload->userId);

   try
   {
       $data = $post->getAllPosts();
       $message = $data;
   }
   catch(Exception $e)
   {
       $message = $e->getMessage();
   }

    echo $message;exit;

}
catch(Exception $e)
{
   echo $e->getmessage();exit;
}
