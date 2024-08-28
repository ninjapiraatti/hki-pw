<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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
  $page->of(false);
  $page->title = $_POST['name'];
  $page->body = $_POST['bio'];
  $page->name = $_POST['id'];
  $page->attribute_strength = $_POST['strength'];
  $page->attribute_perception = $_POST['perception'];
  $page->attribute_endurance = $_POST['endurance'];
  $page->attribute_charisma = $_POST['charisma'];
  $page->attribute_intelligence = $_POST['intelligence'];
  $page->attribute_agility = $_POST['agility'];
  $page->attribute_luck = $_POST['luck'];
  $page->save();
  if (isset($uploaded)) {
    foreach ($page->images as $image) {
      $page->images->remove($image); // Remove the current image
    }
    $page->save();
    $filePath = $tempDir . $uploaded[0];
    $page->images->add($filePath);
    $page->save();
  }
  $response = [
    'received' => $_POST,
    'message' => 'Data received successfully.',
  ];

  echo json_encode($response);
  exit;
}
foreach($page->images as $image) {
  $imageUrl = $image->url;
}
$data = [
  'name' => $page->title,
  'bio' => $page->body,
  'image' => $imageUrl,
  'strength' => $page->attribute_strength,
  'perception' => $page->attribute_perception,
  'endurance' => $page->attribute_endurance,
  'charisma' => $page->attribute_charisma,
  'intelligence' => $page->attribute_intelligence,
  'agility' => $page->attribute_agility,
  'luck' => $page->attribute_luck,
];
return wireEncodeJSON($data);

?>