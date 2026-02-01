<?php

namespace ProcessWire;

require_once wire('config')->paths->AppApi . 'vendor/autoload.php';
require_once wire('config')->paths->AppApi . 'classes/AppApiHelper.php';

require_once __DIR__ . '/ApiException.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Article.php';
require_once __DIR__ . '/Character.php';
require_once __DIR__ . '/Thing.php';

$routes = [
	'articles' => [
		['OPTIONS', '', ['GET', 'POST']],
		['GET', '', Article::class, 'getArticles', ['auth' => true]],
		['POST', '', Article::class, 'createArticle', ['auth' => true]],
		['OPTIONS', '{id:\d+}', ['GET', 'PUT', 'DELETE']],
		['GET', '{id:\d+}', Article::class, 'getArticle', ['auth' => true]],
		['PUT', '{id:\d+}', Article::class, 'updateArticle', ['auth' => true]],
		['DELETE', '{id:\d+}', Article::class, 'deleteArticle', ['auth' => true]],
	],

	'characters' => [
		['OPTIONS', '', ['GET', 'POST']],
		['GET', '', Character::class, 'getCharacters', ['auth' => true]],
		['POST', '', Character::class, 'createCharacter', ['auth' => true]],
		['OPTIONS', '{id:\d+}', ['GET', 'PUT', 'DELETE']],
		['GET', '{id:\d+}', Character::class, 'getCharacter', ['auth' => true]],
		['PUT', '{id:\d+}', Character::class, 'updateCharacter', ['auth' => true]],
		['DELETE', '{id:\d+}', Character::class, 'deleteCharacter', ['auth' => true]],
	],

	'things' => [
		['OPTIONS', '', ['GET', 'POST']],
		['GET', '', Thing::class, 'getThings', ['auth' => true]],
		['POST', '', Thing::class, 'createThing', ['auth' => true]],
		['OPTIONS', '{id:\d+}', ['GET', 'PUT', 'DELETE']],
		['GET', '{id:\d+}', Thing::class, 'getThing', ['auth' => true]],
		['PUT', '{id:\d+}', Thing::class, 'updateThing', ['auth' => true]],
		['DELETE', '{id:\d+}', Thing::class, 'deleteThing', ['auth' => true]],
	],
];
