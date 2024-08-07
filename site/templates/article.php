<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: *"); // Change '*' to your Vue app's origin for production
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");


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
		$building = $pages->add('articles', '/hki-pw/this-is-htmx-test', $data['foo']);
    // Send the response back as JSON
    echo json_encode($response);
    exit; // Important to exit after sending the response
}
//return $PEI->exportJSON($page->children());
//return json_encode($postThing);
//return json_encode($input->post('foo'));
?>