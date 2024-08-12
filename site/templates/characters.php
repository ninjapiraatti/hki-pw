<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

/*
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //$tempDir = wire()->files->tempDir('userUploads')->get();
  $tempDir = "/Applications/MAMP/tmp/php";
  if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
      if (move_uploaded_file($_FILES['image']['tmp_name'], $tempDir . $_FILES['image']['name'])) {
        echo "File uploaded successfully: " . $_FILES['image']['name'];
      } else {
        echo "File upload error: " . $_FILES['image']['error'];
      }
  } else {
      echo "File upload error: " . $_FILES['image']['error'];
  }
  exit;
} else {
  echo "Invalid request method";
}
*/

if ($input->is('post')) {
  /*
  $json = file_get_contents('php://input');
  $data = json_decode($json, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
      echo json_encode(['error' => 'Invalid JSON']);
      exit;
  }
      */

  //$tempDir = "/Applications/MAMP/tmp/";
  $tempDir = wire()->files->tempDir('userUploads')->get();

  //var_dump(isset($_FILES['image']));
  //exit;
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
    
    foreach ($uploaded as $file) {
      $filePath = $tempDir . $file;
      echo $filePath . "<br>";
      //var_dump($filePath);
      //exit;
    }

    if (count($uploaded)) {
      echo sprintf("Uploaded %d files", count($uploaded));
    }
  } else {
    echo json_encode(['error' => 'File upload error']);
    exit;
  }

  // $page = $pages->get(1234);
  /*
  foreach ($uploaded as $file) {
      $filePath = $tempDir . $file;
      // $page->files->add($filePath);

      echo $filePath . "<br>";
  }
      */
  // $page->save('files');

  if (count($uploaded)) {
      echo sprintf("Uploaded %d files", count($uploaded));
      $showForm = false;
  } else {
    echo "no files";
    exit;
  }

  /*
  $response = [
      'received' => $data,
      'message' => 'Data received successfully.',
  ];
  */
  /*
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
  */
  $character = $pages->add('character', '/hki-pw/characters', [
    'title' => $_POST['name'], // Use $_POST for other fields
    'body' => $_POST['bio'],
    //'images' => isset($uploaded[0]) ? $uploaded[0] : null, // Use the uploaded file
    'attribute_strength' => $_POST['strength'],
    'attribute_perception' => $_POST['perception'],
    'attribute_endurance' => $_POST['endurance'],
    'attribute_charisma' => $_POST['charisma'],
    'attribute_intelligence' => $_POST['intelligence'],
    'attribute_agility' => $_POST['agility'],
    'attribute_luck' => $_POST['luck']
  ]);
  $filePath = $tempDir . $uploaded[0];
  $character->images->add($filePath);
  $character->save();
  $response = [
    'received' => $_POST,
    'message' => 'Data received successfully.',
  ];


  echo json_encode($response);
  exit;
}
?>