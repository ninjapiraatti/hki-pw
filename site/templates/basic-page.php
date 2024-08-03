<?php namespace ProcessWire; 

// Template file for pages using the “basic-page” template
// -------------------------------------------------------
// The #content div in this file will replace the #content div in _main.php
// when the Markup Regions feature is enabled, as it is by default. 
// You can also append to (or prepend to) the #content div, and much more. 
// See the Markup Regions documentation:
// https://processwire.com/docs/front-end/output/markup-regions/
/*
$page = $pages->add(
	'basic-page',
	'/this-is-htmx-test',
	'new-test',
);
*/
$PEI = new PagesExportImport();
$postThing = $input->is("post");
//return $PEI->exportJSON($page->children());
return json_encode($postThing);
?>