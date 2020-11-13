<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../authToken.php');
include_once('../post.php');
include_once('../validator.php');
include_once('../vendor/autoload.php');
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


if (isset($_SERVER["HTTP_ORIGIN"])) {
    header("Access-Control-Allow-Origin: {$_SERVER["HTTP_ORIGIN"]}");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 0");
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With,Referer,User-Agent,Access-Control-Allow-Origin');
    http_response_code(200);
  }
  if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"])) header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    //if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) header("Access-Control-Allow-Headers: {" . $_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"] ."}");
    $request = Request::createFromGlobals();
    $response = new Response();
    $response->setStatusCode(200);
    $response->prepare($request);
    $response->send();
  }
  header("Content-Type: application/json; charset=UTF-8");

$validator = new Validator;
$validator->validateRequestType('POST');

$db = new database;
$dbConn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

$title = $data->title;
$description = $data->description;


$validator->validateParameter('Title',$title,STRING,'50','5');
$validator->validateParameter('Description',$description,STRING,'200','19');


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
    $post->setCreatedAt(date('Y-m-d h:i:sa'));
    $post->setUpdatedAt(date('Y-m-d h:i:sa'));

    try
    {
        $post->insert();
        $message = "inserted post into database";
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
