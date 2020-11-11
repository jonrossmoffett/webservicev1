<?php 
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
           
		}

		public function insert() {

            

            $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE email = :email';
            $stmt = $this->dbConn->prepare($sql);
            $stmt->bindParam(':email', $this->Email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_OBJ);


            if(empty($user)){
                echo " reached queory ";
                $sql = 'INSERT INTO ' . $this->tableName . '(name, email , password) VALUES(:name, :email, :password)';
                $stmt = $this->dbConn->prepare($sql);
                $stmt->bindParam(':name', $this->Name);
                $stmt->bindParam(':email', $this->Email);
                $stmt->bindParam(':password', $this->Password);
                $stmt->execute();

                $this->returnResponse(EMAIL_TAKEN,'User Created'); 
            }else{
                $this->returnResponse(EMAIL_TAKEN,'Email is taken'); 
            }


/* 
			$sql = 'INSERT INTO ' . $this->tableName . '(title, description, user_id, status , created_at, updated_at) VALUES(:title, :description, :user_id , :status, :created_at, :updated_at)';

			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindParam(':title', $this->Title);
			$stmt->bindParam(':description', $this->Description);
            $stmt->bindParam(':status', $this->Status);
            $stmt->bindParam(':user_id', $this->createdBy);
			$stmt->bindParam(':created_at', $this->createdAt);
			$stmt->bindParam(':updated_at', $this->updatedAt);
			
			if($stmt->execute()) {
				return true;
			} else {
				return false;
            } */
            
		}


        public function returnResponse($code, $data) {
			header("content-type: application/json");
			$response = json_encode(['response' =>['status'=>$code,"result" => $data]]);
			echo $response;exit;
		}
	}
 ?>