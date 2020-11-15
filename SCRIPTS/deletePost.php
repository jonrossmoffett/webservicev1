<?php
$curl = curl_init();

$request = '{
    "name" : "generateToken",
    "param" : {
        "email": "user@app.com",
        "pass": "password"
    }
}';


curl_setopt($curl,CURLOPT_URL,'https://phpwebservice.herokuapp.com/');
curl_setopt($curl,CURLOPT_POST,true);
curl_setopt($curl,CURLOPT_HTTPHEADER,['content-type: application/json']);
curl_setopt($curl,CURLOPT_POSTFIELDS,$request);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

$result = curl_exec($curl);
$err = curl_error($curl);

if($err){
    echo 'Curl Error' . $err;
}else{
    header(('content-type: application/json'));
    $response = json_decode($result,true);
    $token = $response['response']['result']['token'];
}

curl_close($curl);

$curl = curl_init();

$request = '{
    "name" : "GetUserPosts",
    "param" : {}
}';

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://phpwebservice.herokuapp.com",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_POSTFIELDS => $request,
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer ". $token,
      "Content-Type: application/json"
    ),
  ));
  
  $result = curl_exec($curl);
  $err = curl_error($curl);

    if($err){
    echo 'Curl Error' . $err;
    }else{
    header(('content-type: application/json'));
    $response = json_decode($result,true);
    $deleteId = $response['response']['result'][0]['id'];
    }

  curl_close($curl);
  
  
$curl = curl_init();

$request = '{
    "name" : "deletePost",
    "param" : {"PostId" : '. $deleteId . '}}';

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://phpwebservice.herokuapp.com",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $request,
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer " . $token,
    "Content-Type: application/json",
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

if($err){
    echo 'Curl Error' . $err;
    }else{
    header(('content-type: application/json'));
    $response = json_decode($response,true);
    print_r($response) ;
    }

curl_close($curl);
