<?php namespace ProcessWire;
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

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