<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../constants.php');
include_once('../validator.php');
include_once('../user.php');

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (isset($_SERVER["HTTP_ORIGIN"])) {
    header("Access-Control-Allow-Origin: {$_SERVER["HTTP_ORIGIN"]}");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 0");
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With,Referer,User-Agent,Access-Control-Allow-Origin');
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
        $validator->validateRequestType('PUT');
        $db = new database;
        $dbConn = $db->connect();
        $data = json_decode(file_get_contents("php://input"));
    
        $email = $data->email;
        $name = $data->name;
        $password = $data->password;
        
        $validator->validateParameter('Email',$email, EMAIL ,50,5,TRUE);
        $validator->validateParameter('Password',$password,PASSWORD,20,8,TRUE);
        $validator->validateParameter('Name',$name,STRING,30,3,TRUE);

        try {
            $user = new User;
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setName($name);
            $user->setCreatedAt(date('Y-m-d'));
            $user->setEmailVerifiedAt(date('Y-m-d'));
            $user->setUpdatedAt(date('Y-m-d'));
            $user->insert();
            $message = "inserted post into database";
        }catch(Exception $e){
            $message = $e->getMessage();
        }

        $response = json_encode(['response' => $message]);
        echo $response;exit;

    ?>