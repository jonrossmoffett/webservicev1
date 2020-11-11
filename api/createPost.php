<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../authToken.php');
include_once('../post.php');
include_once('../validator.php');

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


$title = $data->title;
$description = $data->description;

$validator = new Validator;
$validator->validateParameter('Title',$title,STRING,'200','5',TRUE,FALSE,FALSE);
$validator->validateParameter('Description',$description,STRING,'200','5',TRUE,FALSE,FALSE);


$authCheck = new AuthTokenChecker;
$token = $authCheck->getBearerToken();

$uid = $authCheck->validateToken($token);

try 
{
    $post = new Post;

    $post->setTitle($title);
    $post->setDescription($description);
    $post->setStatus(0);
    $post->setCreatedBy($uid);
    $post->setCreatedAt(date('Y-m-d'));
    $post->setUpdatedAt(date('Y-m-d'));

    try
    {
        $post->insert();
        $message = "inserted into db";
    }
    catch(Exception $e)
    {
        $message = $e->getMessage();
    }

    $response = json_encode(['response' => $message]);
    echo $response;exit;

}
catch(Exception $e)
{
   echo $e->getmessage();exit;
}
