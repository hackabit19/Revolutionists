<?php

require 'loader.php';
try {
  $index_para['index']  = 'places';
  $mapping = [
    '_source' => [  'enabled' => true],
    'properties' => [
      'from_coords' => ['type' => 'geo_point'],
      'to_coords' => ['type' => 'geo_point'],
      'current_coords' => ['type' => 'geo_point'],
      'from_bounds.top0_left.coords' => ['type' => 'geo_point'],
      'from_bounds.bottom_right.coords' => ['type' => 'geo_point'],
      'to_bounds.top_left.coords' => [
        'type' => 'geo_point'
      ],
      'to_bounds.bottom_right.coords' => [
        'type' => 'geo_point']
    ]
  ];
  
  $index_Para ['body']['mappings']['location'] = $mapping;
  
  $response = $client->indices()->create($index_Para);
  print_r($response);
  


} catch(\Exception $e) {
  echo 'err: ' . $e->getMessage();
}
