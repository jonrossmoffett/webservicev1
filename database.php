<?php
 
class database {
    private $host = 'ec2-54-91-178-234.compute-1.amazonaws.com';
    private $db_name = 'danh5kcohmgccn';
    private $username = 'cmutaimxdtwmad';
    private $password = '9cce09313a2575bca71019baaa1c847c82cf154aa66d0962fbc7b1a2ceea5797';
    private $conn;

    

    public function connect(){
        $this->conn = null;

        try {
            $this->conn = new PDO('pgsql:host='. $this->host . ';dbname='. $this->db_name, 
            $this->username,$this->password);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            ECHO 'connection error' . $e->getMessage();
        }

        return $this->conn;

    }

}
