<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../authToken.php');
include_once('../post.php');
include_once('../validator.php');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

$validator = new Validator;
$validator->validateRequestType('DELETE');
$db = new database;
$dbConn = $db->connect();
$data = json_decode(file_get_contents("php://input"));


/* $headers = apache_request_headers();
$Auth = $headers['Authorization'];
$Auth = ltrim($Auth,"Bearer"); */

$postId = $data->postId;
$validator->validateParameter('PostId',$postId,INTEGER);


$authCheck = new AuthTokenChecker;
$token = $authCheck->getBearerToken();

$uid = $authCheck->validateToken($token);

try 
{
    $post = new Post;
    $post->setId($postId);
    $post->setCreatedBy($uid);
    
    try
    {
        $post->delete();
        $message = "Deleted post";
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
