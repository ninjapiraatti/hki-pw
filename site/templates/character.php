<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

foreach($page->images as $image) {
  $imageUrl = $image->url;
}
$data = [
  'title' => $page->title,
  'body' => $page->body,
  'images' => $imageUrl,
  'attribute_strength' => $page->attribute_strength,
  'attribute_perception' => $page->attribute_perception,
  'attribute_endurance' => $page->attribute_endurance,
  'attribute_charisma' => $page->attribute_charisma,
  'attribute_intelligence' => $page->attribute_intelligence,
  'attribute_agility' => $page->attribute_agility,
  'attribute_luck' => $page->attribute_luck,
];
echo wireEncodeJSON($data);

?>