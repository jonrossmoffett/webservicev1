<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../constants.php');
include_once('../validator.php');
include_once('../vendor/autoload.php');
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


        $db = new database;
        $dbConn = $db->connect();

        $data = json_decode(file_get_contents("php://input"));
        
        $email = $data->email;
        $password = $data->password;

        $validator = new Validator;
        $validator->validateParameter('Email',$email, EMAIL ,50,5,TRUE);
        $validator->validateParameter('Password',$password,PASSWORD,20,8,TRUE);


        $sql = 'SELECT * FROM users WHERE email = :email';
        $stmt = $dbConn->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if(password_verify($password,$user['password'])){

            $payload = [
                'iat' => time(),
                'iss' => 'localhost',
                'exp' => time() + (60 * 60 * 24),
                'userId' => $user['id']
            ];
            $token = JWT::encode($payload,SECRETE_KEY);
          
        //$var = json_encode($token) ;   
        //echo $var ; exit;

        $request = Request::createFromGlobals();
        $response = new Response();
        $response->setContent(json_encode([$token]));
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);
        $response->prepare($request);
        $response->send();

        }
        else
        {   
            $errors = [];
            array_push($errors,'Incorrect Login Detials');
            $request = Request::createFromGlobals();
            $response = new Response();
            $response->setContent(json_encode(['errors' => $errors]));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(400);
            $response->prepare($request);
            $response->send();
        }   



    





    ?>