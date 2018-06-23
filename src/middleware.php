<?php
// Application middleware

$container = $app->getContainer();
$app->add(new Tuupola\Middleware\JwtAuthentication([
    'path' => '/',
    "ignore" => ["/users/register", "/users/authenticate"],
    "secret" => $container->get('settings')['jwtAuthentication']['secret'],
    'algorithm' => 'HS256',
    'before' => function ($request, $arguments) use ($container) {
        $container["jwt"] = $arguments["decoded"];
    }
]));
