<?php

namespace ProcessWire;

/**
 * Adds the draft fields to the character template. Safe to run more than once.
 *
 * Run: ddev exec php site/migrations/add-character-draft-fields.php
 */

if (PHP_SAPI !== 'cli') exit(1);

include __DIR__ . '/../../index.php';

$template = wire('templates')->get('character');
$fieldgroup = $template->fieldgroup;

$draft = wire('fields')->get('draft');
if (!$draft) {
	$draft = new Field();
	$draft->type = wire('modules')->get('FieldtypeTextarea');
	$draft->name = 'draft';
	$draft->label = 'Draft';
	$draft->description = 'Unpublished changes saved from the app as JSON. Managed by the API.';
	$draft->collapsed = Inputfield::collapsedYes;
	$draft->save();
	echo "Created field 'draft'\n";
}

$draftImage = wire('fields')->get('draft_image');
if (!$draftImage) {
	$draftImage = new Field();
	$draftImage->type = wire('modules')->get('FieldtypeImage');
	$draftImage->name = 'draft_image';
	$draftImage->label = 'Draft portrait';
	$draftImage->description = 'Portrait saved with the draft. Managed by the API.';
	$draftImage->maxFiles = 1;
	$draftImage->outputFormat = FieldtypeFile::outputFormatArray;
	$draftImage->extensions = wire('fields')->get('images')->extensions;
	$draftImage->collapsed = Inputfield::collapsedYes;
	$draftImage->save();
	echo "Created field 'draft_image'\n";
}

foreach ([$draft, $draftImage] as $field) {
	if (!$fieldgroup->hasField($field)) {
		$fieldgroup->add($field);
		echo "Added '{$field->name}' to template 'character'\n";
	}
}
$fieldgroup->save();

echo "Done\n";
