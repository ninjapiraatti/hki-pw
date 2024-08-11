<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

if ($input->is('post')) {
	$json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Invalid JSON']);
        exit;
    }
    $response = [
        'received' => $data,
        'message' => 'Data received successfully.',
    ];
		$building = $pages->add('character', '/hki-pw/characters', $data['title']);
    echo json_encode($response);
    exit;
}
?>