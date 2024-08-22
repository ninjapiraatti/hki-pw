<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	// Handle preflight requests
	header("HTTP/1.1 204 No Content");
	exit;
}

// Template file for pages using the “basic-page” template
// -------------------------------------------------------
// The #content div in this file will replace the #content div in _main.php
// when the Markup Regions feature is enabled, as it is by default. 
// You can also append to (or prepend to) the #content div, and much more. 
// See the Markup Regions documentation:
// https://processwire.com/docs/front-end/output/markup-regions/
  http_response_code(404); // Set the HTTP response code to 404
  wire('log')->save('character', 'Debug info: ' . 'lol');
  return wireEncodeJSON(['error' => 'Character not found.']);
  exit;

?>

<div id="content">
	Basic page content 
</div>