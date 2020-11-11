<?php

class login extends Rest{

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

}



    ?>