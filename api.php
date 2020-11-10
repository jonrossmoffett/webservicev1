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
        //$logger = new Katzgrau\KLogger\Logger(__DIR__.'/logs');
        //$logger->debug( $_SERVER['HTTP_USER_AGENT']. " with Ip ". $_SERVER['REMOTE_ADDR'] . " generateToken invoked" );
        
        $email = $this->validateParameter('email',$this->param['email'], STRING);
        $pass = $this->validateParameter('pass',$this->param['pass'], STRING);

        try{
        $stmt = $this->dbConn->prepare("SELECT * FROM users WHERE email = :email AND password = :pass");
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(':pass', $pass);

        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!is_array($user)){
            //$logger->debug( $_SERVER['HTTP_USER_AGENT']. " with Ip ". $_SERVER['REMOTE_ADDR'] . " returned error, invalid login credentials" );
            $this->returnResponse(INVALID_USER_PASS, "invalid login credentials");
        }

        $payload = [
            'iat' => time(),
            'iss' => 'localhost',
            'exp' => time() + (60 * 60),
            'userId' => $user['id']
        ];

        $token = JWT::encode($payload,SECRETE_KEY);

        $data = ['token' => $token];
        $this->returnResponse(SUCCESS_RESPONSE,$data);
        
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

    public function GetPostDetails(){

    }

    public function GetUserPosts(){
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

}