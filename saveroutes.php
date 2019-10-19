<?php 
  
    require 'loader.php';

    $googleapikey = getenv('AIzaSyDZYtaMa17r3Jq9IAkEXBWXk_eiujdN-Zk');

    $data = json_decode(file_get_contents("php://input"), true);
    $start_location = $data['start_location']; 
    $end_location = $data['end_location']; 

    $user = $data['user'];
    $from = $data['from']; 
    $to = $data['to']; 
    $id = generateRandomString();

    $steps_data = [];

    $contents = file_get_contents("https://maps.googleapis.com/maps/api/directions/json?origin={$start_location['latitude']},
    {$start_location['longitude']}&destination={$end_location['latitude']},{$end_location['longitude']}&key={$googleapikey}");

          $directions_data = json_decode($contents, true);
    if(!empty($directions_data['routes'])){
        $steps = $directions_data['routes'][0]['legs'][0]['steps'];
        foreach($steps as $step){
          $steps_data[] = [
            'latitude' => $step['start_location']['latitude'],
            'longitude'=> $step['start_location']['longitude']
          ];
  
          $steps_data[] = [
            'latitude' => $step['end_location']['latitude'],
            'longitude' => $step['end_location']['longitude']
          ];
        }
      }
          if(!empty($directions_data['routes'])){
        $steps = $directions_data['routes'][0]['legs'][0]['steps'];
        foreach($steps as $step){
          $steps_data[] = [
            'latitude' => $step['start_location']['latitude'],
            'longitude' => $step['start_location']['longitude']
          ];
  
                 $steps_data[] = [
            'latitude' => $step['end_location']['latitude'],
            'longitude' => $step['end_location']['longitude']
          ];
        }
      }

      if(!empty($steps_data)){

        $params = [
          'index' => 'places',
          'type' => 'location',
          'id' => $id,
          'body' => [
            'user' => $user, 
            'from' => $from, 
            'to' => $to,
            'from_coords' => [ 
              'latitude' => $start_location['latitude'],
              'longitude' => $start_location['longitude'],
            ],
            'current_coords' => [
              'latitude' => $start_location['latitude'],
              'longitude' => $start_location['longitude'],
            ],
            'to_coords' => [
              'latitude' => $end_location['latitude'],
              'longitude' => $end_location['longitude'],
            ],
            'steps' => $steps_data
          ]
        ];}  try{
        $response = $client->index($params);
        $response_data = json_encode([
          'id' => $id
        ]);
  
        echo $response_data;
      }catch(\Exception $e){
        echo 'err: ' . $e  -> getMessage();
      } 
