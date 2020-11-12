<?php
include_once('../database.php');
include_once('../jwt.php');
include_once('../constants.php');
include_once('../validator.php');
include_once('../user.php');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

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