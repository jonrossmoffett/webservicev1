<?php 

include_once('validator.php');
include_once('jwt.php');


	class User {
		private $id;
        private $Name;
        private $Email;
		private $Password;
        private $EmailVerifiedAt;
        private $CreatedAt;
		private $UpdatedAt;
		private $tableName = 'users';
        private $dbConn;
        private $validator;
        private $defaultRole = 'App\Models\User';
        private $defaultRoleId = 3;

		function setId($id) { $this->id = $id; }
        function getId() { return $this->id; }
        
		function setName($Name) { $this->Name = $Name; }
        function getName() { return $this->Name; }

        function setEmail($Email) { $this->Email = $Email; }
        function getEmail() { return $this->Email; }
        
        function setPassword($Password) { $this->Password = $Password; }
        function getPassword() { return $this->Password; }

        function setCreatedAt($CreatedAt) { $this->CreatedAt = $CreatedAt; }
        function getCreatedAt() { return $this->CreatedAt; }

        function setEmailVerifiedAt($EmailVerifiedAt) { $this->CreatedAt = $EmailVerifiedAt; }
        function getEmailVerifiedAt() { return $this->EmailVerifiedAt; }

        function setUpdatedAt($UpdatedAt) { $this->UpdatedAt = $UpdatedAt; }
        function getUpdatedAt() { return $this->UpdatedAt; }

		public function __construct() {
			$db = new database;
            $this->dbConn = $db->connect();
            $this->validator = new Validator();
		}

		public function insert() {

            //hash pass
            $hashedPassword = password_hash($this->getPassword(), PASSWORD_DEFAULT);
            $this->setPassword($hashedPassword);
            //check if user exists with that email
            $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE email = :email';
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindParam(':email', $this->Email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            //if no user exists, add them to the database
            if(empty($user)){
                
                $sql = 'INSERT INTO ' . $this->tableName . '(name, email , password, created_at, email_verified_at, updated_at) VALUES(:name, :email, :password, :created_at, :email_verified_at, :updated_at)';
                $stmt = $this->dbConn->prepare($sql);
                $stmt->bindParam(':name', $this->Name);
                $stmt->bindParam(':email', $this->Email);
                $stmt->bindParam(':password', $this->Password);
                $stmt->bindParam(':created_at',$this->CreatedAt);
                $stmt->bindParam(':email_verified_at',$this->EmailVerifiedAt);
                $stmt->bindParam(':updated_at',$this->UpdatedAt);
                $stmt->execute();
                
                //retrieve the user we just created
                $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE email = :email';
                $stmt = $this->dbConn->prepare($sql);
                $stmt->bindParam(':email', $this->Email);
                $stmt->execute();
                $user = $stmt->fetch();
                $this->id = $user['id'];
                
                //take the id from the user we created and assign him a role
                $sql = 'INSERT INTO ' . 'role_user'. '(role_id, user_id , user_type) VALUES(:role_id, :user_id, :user_type)'; 
                $stmt = $this->dbConn->prepare($sql);
                $stmt->bindParam(':role_id', $this->defaultRoleId );
                $stmt->bindParam(':user_id', $this->id );
                $stmt->bindParam(':user_type', $this->defaultRole);
                $stmt->execute();  

                //prepare JWT
                $payload = [
                    'iat' => time(),
                    'iss' => 'localhost',
                    'exp' => time() + (60 * 60 * 24),
                    'userId' => $this->id
                ];
                $token = JWT::encode($payload,SECRETE_KEY);
                $this->validator->response(200,$token);
            
            }
            else
            {
                $this->validator->response(400,'Email is taken');
            }

            
		}


        public function returnResponse($code, $data) {
			header("content-type: application/json");
			$response = json_encode(['response' =>['status'=>$code,"result" => $data]]);
			echo $response;exit;
		}
	}
 ?>