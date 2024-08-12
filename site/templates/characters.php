<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

if ($input->is('post')) {
  $tempDir = wire()->files->tempDir('userUploads')->get();
  if (isset($_FILES['image'])) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
      echo json_encode(['error' => 'File upload error: ' . $_FILES['image']['error']]);
      exit;
    }
    $uploaded = (new WireUpload('image'))
      ->setValidExtensions(['txt', 'png', 'jpg', 'jpeg', 'pdf'])
      ->setMaxFiles(1)
      ->setMaxFileSize(10 * pow(2, 20)) // 10MB
      ->setDestinationPath($tempDir)
      ->setOverwrite(true)
      ->execute();

    wire('log')->save('character', 'Debug info: ' . count($uploaded) . ' | _FILES: ' . print_r($_FILES, 1));
  }
  $character = $pages->add('character', '/hki-pw/characters', [
    'title' => $_POST['name'],
    'body' => $_POST['bio'],
    'attribute_strength' => $_POST['strength'],
    'attribute_perception' => $_POST['perception'],
    'attribute_endurance' => $_POST['endurance'],
    'attribute_charisma' => $_POST['charisma'],
    'attribute_intelligence' => $_POST['intelligence'],
    'attribute_agility' => $_POST['agility'],
    'attribute_luck' => $_POST['luck']
  ]);
  if (isset($uploaded)) {
    $filePath = $tempDir . $uploaded[0];
    $character->images->add($filePath);
    $character->save();
  }
  $response = [
    'received' => $_POST,
    'message' => 'Data received successfully.',
  ];

  echo json_encode($response);
  exit;
}
?>