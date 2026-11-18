<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: *"); // Change '*' to your Vue app's origin for production
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

// Template file for pages using the “basic-page” template
// -------------------------------------------------------
// The #content div in this file will replace the #content div in _main.php
// when the Markup Regions feature is enabled, as it is by default. 
// You can also append to (or prepend to) the #content div, and much more. 
// See the Markup Regions documentation:
// https://processwire.com/docs/front-end/output/markup-regions/
$PEI = new PagesExportImport();
$postThing = false;
if ($input->is('post')) {
	$json = file_get_contents('php://input');

    // Decode the JSON data
    $data = json_decode($json, true); // true for associative array

    // Check if decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Handle JSON decoding error
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }

    // Output the received data for debugging
    // You can access individual elements like $data['foo'] if your JSON has a 'foo' key
    $response = [
        'received' => $data, // Echo back the received data
        'message' => 'Data received successfully.',
    ];
		$building = $pages->add('basic-page', '/hki-pw/this-is-htmx-test', $data['foo']);
    // Send the response back as JSON
    echo json_encode($response);
    exit; // Important to exit after sending the response
}
//return $PEI->exportJSON($page->children());
//return json_encode($postThing);
//return json_encode($input->post('foo'));
?>