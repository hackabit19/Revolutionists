<?php

require 'loader.php' ;


$data= json_decode( file_get_contents("php://input" ) , true);

$user= $data['user' ];

$para=[


'index' => 'places',
'type'=> 'users',


];

$para['body']['query']['match']['user'] = $user;

try{


    $search=$client -> search($para);

if($search['hits']['total']==0)
{

$response=$client -> index(

[

'index'=> 'places' ,
'type'=> 'users',
'id'=> $user,
'body' => [
    'user' => $user
]
]

);

}

}

   echo 'ok';
} 
catch(\Exception $e){
    echo 'err:'.$e->getMessage();
}
