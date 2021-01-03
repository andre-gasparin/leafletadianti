<?php
call_user_func($_GET['method'], $_GET['term']['term']);  
function SearchAddress($value){

    $value  = str_replace(' ', '%20', $value);
    $content =     file_get_contents("https://photon.komoot.io/api/?q=".$value);
    $response = json_decode($content, true);

     foreach ( $response['features'] as $feature)
     {
         if(!empty($feature['properties']['city']))
         {
             $coord = $feature['geometry']['coordinates'][1].','.$feature['geometry']['coordinates'][0];
             $name = '';
             $name = (!empty($feature['properties']['name'])) ? $name . $feature['properties']['name'] : '';
             $name = (!empty($feature['properties']['city'])) ? $name . ', ' . $feature['properties']['city'] : '';
             $name = (!empty($feature['properties']['state'])) ? $name . ' - ' . $feature['properties']['state'] : '';
             $result[] = ["id"=>  $coord, "text"=>  $name];
         }
     }
     echo json_encode($result);
     return;
}