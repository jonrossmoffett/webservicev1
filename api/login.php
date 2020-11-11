<?php
include_once('../database.php');
include_once('../jwt.php');


header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
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

        if(empty($email)){
            array_push($validationErrors,"Please provide an email");
            $isValidationError = true;
        }
        if(empty($password)){
            array_push($validationErrors,"Please provide a password");
            $isValidationError = true;
        }

        if($isValidationError == true){
            header("content-type: application/json");
            $response = json_encode(['errors' => $validationErrors ]);
			echo $response;exit;
        }


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
    
            $data = ['token' => $token];
			echo $data; exit;

        }else{
            array_push($validationErrors,"Incorrect Login Detials");
            header("content-type: application/json");
            $response = json_encode(['errors' => $validationErrors ]);
			echo $response;exit;
        }   



    





    ?>