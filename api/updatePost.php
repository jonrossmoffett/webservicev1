<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../authToken.php');
include_once('../post.php');
include_once('../validator.php');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

$validator = new Validator;
$validator->validateRequestType('PATCH');

$db = new database;
$dbConn = $db->connect();

$data = json_decode(file_get_contents("php://input"));


$title = $data->title;
$description = $data->description;
$postId = $data->postId;


$validator->validateParameter('Title',$title,STRING,'200','5');
$validator->validateParameter('Description',$description,STRING,'200','5');
$validator->validateParameter('PostId',$postId,INTEGER);

$authCheck = new AuthTokenChecker;
$token = $authCheck->getBearerToken();

$uid = $authCheck->validateToken($token);

try 
{
    $post = new Post;

    $post->setId($postId);
    $post->setTitle($title);
    $post->setDescription($description);
    $post->setStatus(0);
    $post->setCreatedBy($uid);
    $post->setCreatedAt(date('Y-m-d'));
    $post->setUpdatedAt(date('Y-m-d'));

    try
    {
        $post->updatePost();
        $message = "Updated post";
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
