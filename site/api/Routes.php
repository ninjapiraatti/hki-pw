<?php

namespace ProcessWire;

require_once wire('config')->paths->AppApi . 'vendor/autoload.php';
require_once wire('config')->paths->AppApi . 'classes/AppApiHelper.php';

require_once __DIR__ . '/Example.php';
require_once __DIR__ . '/Article.php';
require_once __DIR__ . '/Thing.php';

$routes = [
	['OPTIONS', 'test', ['GET']], // this is needed for CORS Requests
	['GET', 'test', Example::class, 'test'],

	'users' => [
		['OPTIONS', '', ['GET']], // this is needed for CORS Requests
		['GET', '', Example::class, 'getAllUsers', ['auth' => true]],
		['OPTIONS', '{id:\d+}', ['GET']], // this is needed for CORS Requests
		['GET', '{id:\d+}', Example::class, 'getUser', ['auth' => true]], // check: https://github.com/nikic/FastRoute
	],
  'articles' => [
		['OPTIONS', '', ['GET']],
		['GET', '', Article::class, 'getArticles', ['auth' => true]],
		['OPTIONS', '{id:\d+}', ['GET']],
		['GET', '{id:\d+}', Article::class, 'getArticle', ['auth' => true]],
	],
	'things' => [
		['OPTIONS', '', ['GET']],
		['GET', '', Thing::class, 'getThings', ['auth' => true]],
		['OPTIONS', '{id:\d+}', ['GET']],
		['GET', '{id:\d+}', Thing::class, 'getThing', ['auth' => true]],
	],
];