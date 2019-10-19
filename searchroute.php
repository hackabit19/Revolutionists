<?php 
    
    require 'loader.php';

    $googleapikey = getenv('AIzaSyDZYtaMa17r3Jq9IAkEXBWXk_eiujdN-Zk');

    $params['index'] = 'places';
    $params['type'] = 'location';

    $data = json_decode(file_get_contents("php://input"), true);

    
    $rider_origin_lat = $data['origin']['latitude'];
    $rider_origin_lon = $data['origin']['longitude'];

    $rider_dest_lat = $data['dest']['latitude'];
    $rider_dest_lon = $data['dest']['longitude'];

    $rider_directions = file_get_contents("https://maps.googleapis.com/maps/api/directions/json?origin={$rider_origin_lat},{$rider_origin_lon}&destination={$rider_dest_lat},{$rider_dest_lon}&key={$googleapikey}");

    $riderdirections_data = json_decode($rider_directions, true);


    $hikers_steps = [];

    $steps = $riderdirections_data['routes'][0]['legs'][0]['steps']; 
    foreach($steps as $index => $s){
      if($index == 0){ 
        $rider_steps[] = [
          'latitude' => $s['start_location']['latitude'],
          'longitude' => $s['start_location']['longitude']
        ];  
      }

      $rider_steps[] = [
        'latitude' => $s['end_location']['latitude'],
        'longitude' => $s['end_location']['longitude']
      ];
    }
    $params['body'] = [
      "min_score" => 0.5,
      'query' => [
        'function_score' => [
          'gauss' => [
            'current_coords' => [  "origin" => ["lat" => $rider_origin_lat, "lon" => $rider_origin_lon],
            "offset" => "100m",
            "scale" => "5km" ]
            ]
          ]
        ]
      ];

      $sharer_origin = ['latitude' => $sharer_origin_lat, 'longitude' => $sharer_origin_longitude];
      $sharer_dest = ['latitude' => $sharer_dest_lat, 'longitude' => $sharer_dest_longitude];
      try {
        $response = $client->search($params);
  
        if(!empty($response['hits']) && $response['hits']['total'] > 0){
          foreach($response['hits']['hits'] as $hit){
  
            $source = $hit['_source'];
            $sharer_steps = $source['steps'];
  
            $current_coords = $source['current_coords'];
            $to_coords = $source['to_coords'];
  
            $sharer_origin = [
              'latitude' => $current_coords['latitude'],
              'longitude' => $current_coords['longitude']
            ];
  
            $sharer_dest = [
              'latitude' => $to_coords['latitude'],
              'longitude' => $to_coords['longitude']
            ];
  
            
            if(isCoordsOnPath($rider_origin_lat, $rider_origin_lon, $sharer_steps) && canDropoff($rider_origin, $rider_dest, $sharer_origin, $sharer_dest, $rider_steps, $sharer_steps)){
              
              $sharer_details = [
                'user' => $source['user'],
                'from' => $source['from'],
                'to' => $source['to']
              ];
  
              echo json_encode($sharer_details);     
              break; 
            }
          }
        }
  
      } catch(\Exception $e) {
        echo 'err: ' . $e->getMessage();
      }
      function isCoordsOnPath($lat, $lon, $path) {
        $response = \GeometryLibrary\PolyUtil::isLocationOnPath(['latitude' => $lat, 'longitude' => $lon], $path, 350); 
        return $response;
      }
      function canDropoff($rider_origin, $rider_dest, $sharer_origin, $sharer_dest, $rider_steps, $sharer_steps) 
      {
        $rider_origin_to_rider_dest = \GeometryLibrary\SphericalUtil::computeDistanceBetween($rider_origin, $rider_dest);
        $rider_origin_to_rider_dest = \GeometryLibrary\SphericalUtil::computeDistanceBetween($rider_origin, $sharer_dest);
        $is_on_path = false;
        if($rider_origin_to_rider_dest > $rider_origin_to_rider_dest){ 
          $is_on_path = isCoordsOnPath($sharer_dest['latitude'], $sharer_dest['longitude'], $sharer_steps); 
  
        }else if($rider_origin_to_rider_dest > $rider_origin_to_rider_dest){ 
          $is_on_path = isCoordsOnPath($rider_dest['latitude'], $rider_dest['longitude'], $sharer_steps);
  
        }else{ 
          $is_on_path = isCoordsOnPath($rider_dest['latitude'], $rider_dest['longitude'], $sharer_steps) || isCoordsOnPath($sharer_dest['lat'], $sharer_dest['lng'], $rider_steps);
        }
  
        return $is_on_path;
  
      }


      }
