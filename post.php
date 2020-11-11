<?php 
	class Post {
		private $id;
        private $Title;
        private $Description;
		private $Status;
        private $updatedAt;
        private $createdAt;
		private $createdBy;
		private $tableName = 'posts';
		private $dbConn;

		function setId($id) { $this->id = $id; }
        function getId() { return $this->id; }
        
		function setTitle($Title) { $this->Title = $Title; }
        function getTitle() { return $this->Title; }

        function setDescription($Description) { $this->Description = $Description; }
        function getDescription() { return $this->Description; }
        
		function setStatus($Status) { $this->Status = $Status; }
        function getStatus() { return $this->Status; }
        
		function setUpdatedAt($updatedAt) { $this->updatedAt = $updatedAt; }
        function getUpdatedAt() { return $this->updatedAt; }
    
		function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
        function getCreatedAt() { return $this->createdAt; }
        
        function setCreatedBy($createdBy) { $this->createdBy = $createdBy; }
        function getCreatedBy() { return $this->createdBy; }

		public function __construct() {
			$db = new database;
			$this->dbConn = $db->connect();
		}

		public function getAllPosts() {
            $stmt = $this->dbConn->prepare("SELECT * FROM " . $this->tableName . " WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $this->createdBy);
			$stmt->execute();
			$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $posts;
		}


		public function insert() {
			
			$sql = 'INSERT INTO ' . $this->tableName . '("Title", "Description", user_id, "Status", created_at, updated_at) VALUES(:title, :description, :user_id , :status, :created_at, :updated_at)';

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
			}
		}

		public function updatePost() {

            $owner = "SELECT * from " . $this->tableName . " WHERE id = :id";
            $stmt1 = $this->dbConn->prepare($owner);
            $stmt1->bindParam(':id', $this->id);
            $stmt1->execute();
            $post = $stmt1->fetch(PDO::FETCH_OBJ);


            if($owner = $post->user_id !== $this->createdBy){
                $this->returnResponse(NOT_OWN_POST,"you do not have access to this post");
            }
            
			
			$sql = "UPDATE $this->tableName SET";
			if( null != $this->getTitle()) {
				$sql .=	" Title = '" . $this->getTitle() . "',";
			}

			if( null != $this->getDescription()) {
				$sql .=	" Description = '" . $this->getDescription() . "',";
			}

			$sql .=	"updated_at = :updatedAt
					WHERE 
						id = :userId";

			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindParam(':userId', $this->id);
			$stmt->bindParam(':updatedAt', $this->updatedAt);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		public function delete() {

			$owner = "SELECT * from " . $this->tableName . " WHERE id = :id";
            $stmt1 = $this->dbConn->prepare($owner);
            $stmt1->bindParam(':id', $this->id);
            $stmt1->execute();
            $post = $stmt1->fetch(PDO::FETCH_OBJ);
			$owner = $post;

			if(empty($owner)){
				$this->returnResponse(NOT_OWN_POST,"you do not have access to this post");
			}else{
				if($owner = $post->user_id !== $this->createdBy){
					$this->returnResponse(NOT_OWN_POST,"you do not have access to this post");
				}else{
					
					$stmt = $this->dbConn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :postId');
					$stmt->bindParam(':postId', $this->id);
					
					if($stmt->execute()) {
						return true;
					} else {
						return false;
					}
				}
			}


        }

        public function returnResponse($code, $data) {
			header("content-type: application/json");
			$response = json_encode(['response' =>['status'=>$code,"result" => $data]]);
			echo $response;exit;
		}
	}
 ?>