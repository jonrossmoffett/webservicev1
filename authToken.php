<?php
include_once('database.php');
include_once('constants.php');


class AuthTokenChecker{

    public $dbConn;
    
    public function __construct()
    {
        $db = new database;
        $this->dbConn = $db->connect();
        
    }

    public function validateToken($token) {
        try {
            $payload = JWT::decode($token, SECRETE_KEY, ['HS256']);
            $stmt = $this->dbConn->prepare("SELECT * FROM users WHERE id = :userId");
            $stmt->bindParam(":userId", $payload->userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!is_array($user)) {
                echo "user not in database";exit;
            }

            $this->userId = $payload->userId;
        } catch (Exception $e) {
            echo $e->getMessage();exit;
        }
    }

    public function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
    /**
     * get access token from header
     * */
    public function getBearerToken() {
        $headers = $this->getAuthorizationHeader();
        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }
        echo 'Access Token Not found';exit;
    }


}