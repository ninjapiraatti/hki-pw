<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");
$url = $input->url(); 
  $url = $sanitizer->entities($url); // entity encode for output
  echo "You accessed this page at: $url";

?>