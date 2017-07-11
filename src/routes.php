<?php

$app = \Slim\Slim::getInstance();

// API group
$app->group('/api', function () use ($app) {

	// Vedi: http://www.html5rocks.com/en/tutorials/cors/#toc-adding-cors-support-to-the-server
	$app->options('/(:name+)', function ($name) use ($app) { // Any route segments after the slash should end up in an array called $name.
		$request = $app->request();
		$response = $app->response();
		$origin = $request->headers->get('Origin');
		$response->headers->set('Content-Type', 'application/json; charset=utf-8'); // Va bene anche Content-Type: text/html; charset=utf-8
		$response->headers->set('Access-Control-Allow-Origin', $origin); // Esempio: Access-Control-Allow-Origin: [the same origin from the request]
		$response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE,OPTIONS');
		// Access-Control-Allow-Headers is required if the request has an Access-Control-Request-Headers header
		$request_ca_headers = $request->headers->get('Access-Control-Allow-Headers');
		if ($request_ca_headers) {
			$response->headers->set('Access-Control-Allow-Headers', $request_ca_headers); // the same ACCESS-CONTROL-REQUEST-HEADERS from request
		}
		$response->write("");
	});

	// Validazione cedolino
	$app->post('/validazione-cedolino', 'Iubar\Controllers\Api\ValidazioneCedolino:post');

});