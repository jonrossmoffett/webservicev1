<?php 

class Post{
    private $conn;
    private $table = 'posts';

    public $id;
    public $category_id;
    public $category_name;
    public $title;
    public $body;
    public $author;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //get posts
    public function read(){
        $query = 'SELECT 
        u.name
        p.id,
        p.Title,
        p.Status,
        p.user_id,
        p.Description,
        p.created_at
    FROM
        '. $this->table .' p
        LEFT JOIN
            users u ON p.user_id = u.id
        ORDER BY
            p.created_at DESC';

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
    }
    
}