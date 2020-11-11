<?php

class Api extends Rest{
    public $dbConn;
    
    public function __construct()
    {
        parent::__construct();
        $db = new database;
        $this->dbConn = $db->connect();
        
    }


    public function generateToken(){
        $logger = new Katzgrau\KLogger\Logger(__DIR__.'/logs');
        $logger->debug( $_SERVER['HTTP_USER_AGENT']. " with Ip ". $_SERVER['REMOTE_ADDR'] . " generateToken invoked" );
        
        $email = $this->validateParameter('email',$this->param['email'], STRING);
        $pass = $this->validateParameter('pass',$this->param['pass'], STRING);

        try{
        $stmt = $this->dbConn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if(password_verify($pass,$user['password'])){

            $payload = [
                'iat' => time(),
                'iss' => 'localhost',
                'exp' => time() + (60 * 60 * 24),
                'userId' => $user['id']
            ];
    
            $token = JWT::encode($payload,SECRETE_KEY);
    
            $data = ['token' => $token];
            $this->returnResponse(SUCCESS_RESPONSE,$data);

        }else{
            $this->returnResponse(INVALID_USER_PASS,'invald user pass');
        }   

/*         if(!is_array($user)){
            $logger->debug( $_SERVER['HTTP_USER_AGENT']. " with Ip ". $_SERVER['REMOTE_ADDR'] . " returned error, invalid login credentials" );
            $this->returnResponse(INVALID_USER_PASS, "invalid login credentials");
        } */

        
        }catch(Exception $e){
            $this->throwError(JWT_PROCESSING_ERROR, $e->getmessage());
        }

    }

    public function addPost(){
        $Title = $this->validateParameter('Title',$this->param['Title'], STRING);
        $Description = $this->validateParameter('Description',$this->param['Description'], STRING);

        try {
             $token = $this->getBearerToken();
             $payload = JWT::decode($token,SECRETE_KEY,['HS256']);

             $stmt = $this->dbConn->prepare("SELECT * FROM users WHERE id = :userId");
             $stmt->bindParam(":userId", $payload->userId);
             $stmt->execute();

             $user = $stmt->fetch(PDO::FETCH_ASSOC);
             if(!is_array($user)){
                 $this->returnResponse(INVALID_USER_PASS, "user not found in database");
             }
            
             $post = new Post;
             $post->setTitle($Title);
             $post->setDescription($Description);
             $post->setStatus(0);
             $post->setCreatedBy($payload->userId);
             $post->setCreatedAt(date('Y-m-d'));
             $post->setUpdatedAt(date('Y-m-d'));

            try{
                $post->insert();
                $message = "inserted into db";
            }catch(Exception $e){
                $message = $e->getMessage();
            }

             $this->returnResponse(SUCCESS_RESPONSE,$message);

        }catch(Exception $e){
            $this->throwError(ACCESS_TOKEN_ERRORS, $e->getmessage());
        }

    }

    public function GetUserPosts(){

        if($_SERVER['REQUEST_METHOD'] !== 'GET'){
			$this->throwError(REQUEST_METHOD_NOT_VALID,'Request method is not valid');
        }

        $this->validateToken();
            
        try {
            $token = $this->getBearerToken();
            $payload = JWT::decode($token,SECRETE_KEY,['HS256']);

            $post = new Post;
            $post->setCreatedBy($payload->userId);

           try{
               $data = $post->getAllPosts();
               $message = $data;
           }catch(Exception $e){
               $message = $e->getMessage();
           }

            $this->returnResponse(SUCCESS_RESPONSE,$message);

       }catch(Exception $e){
           $this->throwError(ACCESS_TOKEN_ERRORS, $e->getmessage());
       }
    }

    public function updatePost(){
        $Title = $this->validateParameter('Title',$this->param['Title'], STRING);
        $Description = $this->validateParameter('Description',$this->param['Description'], STRING);
        $PostId = $this->validateParameter('PostId',$this->param['PostId'], INTEGER);

        try {
             $token = $this->getBearerToken();
             $payload = JWT::decode($token,SECRETE_KEY,['HS256']);
             $post = new Post;
             $post->setId($PostId);
             $post->setTitle($Title);
             $post->setDescription($Description);
             $post->setCreatedBy($payload->userId);
             $post->setUpdatedAt(date('Y-m-d'));

            try{
                $post->updatePost();
                $message = "updated post";
            }catch(Exception $e){
                $message = $e->getMessage();
            }

             $this->returnResponse(SUCCESS_RESPONSE,$message);

        }catch(Exception $e){
            $this->throwError(ACCESS_TOKEN_ERRORS, $e->getmessage());
        }
    }

    public function deletePost(){
        $this->validateToken();
        $token = $this->getBearerToken();
        $payload = JWT::decode($token,SECRETE_KEY,['HS256']);
        $PostId = $this->validateParameter('PostId',$this->param['PostId'], INTEGER);
        $post = new Post;
        $post->setId($PostId);
        $post->setCreatedBy($payload->userId);
        try{
            $post->delete();
            $message = "Deleted post";
        }catch(Exception $e){
            $message = $e->getMessage();
        }

        $this->returnResponse(SUCCESS_RESPONSE,$message);

    }

    public function register(){
        $isValidationError = false;
        $ValidationErrors = [];

        $name = $this->validateParameter('name',$this->param['name'], STRING);
        $email = $this->validateParameter('email',$this->param['email'], STRING);
        $password = $this->validateParameter('password',$this->param['password'], STRING);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $isValidationError = true;
            array_push($ValidationErrors,"Email address '$email' is considered valid.\n");
        } 
        if (strlen($name) > 50) {
            $isValidationError = true;
            array_push($ValidationErrors,"name needs to be less than 80 characters");
        }

        $ValidationErrors = json_encode($ValidationErrors);

        if($isValidationError == true){
            header("content-type: application/json");
			$response = json_encode(['errors' => $ValidationErrors]);
			echo $response;exit;
        }

        $user = new User;
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setName($name);
        $user->setCreatedAt(date('Y-m-d'));
        $user->insert();

    }

}