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
    $character = $pages->add('character', '/hki-pw/characters', [
      'title' => $data['name'],
      'body' => $data['bio'],
      'image' => $data['image'],
      'attribute_strength' => $data['strength'],
      'attribute_perception' => $data['perception'],
      'attribute_endurance' => $data['endurance'],
      'attribute_charisma' => $data['charisma'],
      'attribute_intelligence' => $data['intelligence'],
      'attribute_agility' => $data['agility'],
      'attribute_luck' => $data['luck']
    ]);
    echo json_encode($response);
    exit;
}

if ($input->post->upload) {
  echo "uploading";
  $tempDir = wire()->files->tempDir('userUploads')->get();

  $uploaded = (new WireUpload('image')) // same as form field name
      ->setValidExtensions(['txt', 'png', 'jpg', 'pdf'])
      ->setMaxFiles(1) // remove this to allow multiple files
      ->setMaxFileSize(10 * pow(2, 20))// 10MB
      ->setDestinationPath($tempDir)
      ->execute();


  // $page = $pages->get(1234);
  foreach ($uploaded as $file) {
      $filePath = $tempDir . $file;
      // $page->files->add($filePath);

      echo $filePath . "<br>";
  }
  // $page->save('files');

  if (count($uploaded)) {
      echo sprintf("Uploaded %d files", count($uploaded));
      $showForm = false;
  }
}
?>