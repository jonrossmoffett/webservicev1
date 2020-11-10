<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/Post.php';

$database = new Database();
$db = $database->connect();

$post = new Post($db);

$result = $post->read();
$num = $result->rowcount();


if($num > 0) {
    $posts_arr = array();
    $posts_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        $post_item = array(
            'id' => $id,
            'Title' => $title,
            'Description' => $description,
            'user'
        );
    }

}else{

}