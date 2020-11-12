<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../constants.php');
include_once('../validator.php');

header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
header('Content-Type: application/json;');
header('Access-Control-Allow-Methods: POST,OPTIONS,GET,DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');


        $db = new database;
        $dbConn = $db->connect();

$data = json_decode(file_get_contents("php://input"));
        
        $validationErrors = [];
        $isValidationError = false;

        //$email = $this->validateParameter('email',$this->param['email'], STRING);
        //$pass = $this->validateParameter('pass',$this->param['pass'], STRING);

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



    





    ?>