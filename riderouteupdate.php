<?php

require 'loader.php';
$data = json_decode(file_get_contents("php://input"),true);

$para['index' ]='places';
$para['type']= 'location';
$para['id'] =$data['id'];

$latitude = $data['latitude'];
$longitude = $data['longitude' ];

$res= $client -> get($para);
$res['_source']['coordinates']=['latitude' => $latitude,
'longitude' => $longitude ];

$para['body']['doc']=$res['_source' ];
$res = $client -> update($para);

echo json_encode($res);