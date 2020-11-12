<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../constants.php');
include_once('../validator.php');


//header('Access-Control-Allow-Origin: *');
//header('Content-Type: application/json');
//header('Access-Control-Allow-Methods: POST');
//header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With,Referer,User-Agent,Access-Control-Allow-Origin');

if (isset($_SERVER["HTTP_ORIGIN"])) {
    header("Access-Control-Allow-Origin: {$_SERVER["HTTP_ORIGIN"]}");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 0");
  }
  if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"])) header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    //if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) header("Access-Control-Allow-Headers: {" . $_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"] ."}");
    http_response_code(200);
  }
  header("Content-Type: application/json; charset=UTF-8");

  echo 'reached';exit;

/*         $db = new database;
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
			echo $token; exit;
        }else{
            array_push($validationErrors,"Incorrect Login Detials");
            header("content-type: application/json");
            $response = json_encode(['errors' => $validationErrors ]);
			echo $response;exit;
        }   
 */


    





    ?>