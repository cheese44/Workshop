<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

//$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
//    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
//
//    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
//});


//GET
$app->get('/workshops', Cheese44\Workshop\Workshop::class . ':upcoming');
$app->get('/workshops/{id}', Cheese44\Workshop\Workshop::class . ':detail');
$app->get('/workshops/{id}/topics', Cheese44\Workshop\Workshop::class . ':topics');

$app->get('/topics', Cheese44\Workshop\Topic::class . ':outstanding');
$app->get('/topics/{id}', Cheese44\Workshop\Topic::class . ':detail');

//POST
$app->post('/workshops/{id}/participate', Cheese44\Workshop\Workshop::class . ':participate');
$app->post('/workshops/{id}/revoke_participation', Cheese44\Workshop\Workshop::class . ':revokeParticipation');
$app->post('/workshops/{id}/vote', Cheese44\Workshop\Workshop::class . ':vote');

$app->post('/topics', Cheese44\Workshop\Topic::class . ':save');

$app->post('/users/register', Cheese44\Workshop\User::class . ':register');
$app->post('/users/authenticate', Cheese44\Workshop\User::class . ':authenticate');