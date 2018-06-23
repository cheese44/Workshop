<?php

namespace Cheese44\Workshop {

    use Firebase\JWT\JWT;
    use MongoDB\BSON\ObjectId;
    use Psr\Container\ContainerInterface;
    use Slim\Http\Request;
    use Slim\Http\Response;
    use Tuupola\Middleware\JwtAuthentication;

    class User {
        /** @var ContainerInterface $container */
        private $container;

        // constructor receives container instance
        public function __construct(ContainerInterface $container) {
            $this->container = $container;
        }

        public function authenticate(Request $request, Response $response, $args): Response {

            $mail = $request->getParsedBody()['mail'];
            $password = $request->getParsedBody()['password'];

            $userCollection = new \Cheese44\Workshop\Model\User($this->container->get('mongodb'));

            $user = $userCollection->get()->findOne(['mail' => $mail]);

            $response = $response->withStatus(401);
            if($user && $user['password'] == $password) {
                $now = new \DateTime();
                $future = new \DateTime("now +2 hours");

                $jti = base64_encode(random_bytes(16));

                $payload = [
                    "jti" => $jti,
                    "userId" => (string)$user['_id'],
                    "iat" => $now->getTimeStamp(),
                    "exp" => $future->getTimeStamp()
                ];


                $token = JWT::encode($payload, $this->container->get('settings')['jwtAuthentication']['secret']);
                $response = $response->withJson(['token' => $token]);
                $response = $response->withStatus(200);
            }

            return $response;
        }

        public function register(Request $request, Response $response, $args): Response {

            $name = $args['name'];
            $mail = $args['mail'];
            $password = $args['password'];

            $userCollection = new \Cheese44\Workshop\Model\User($this->container->get('mongodb'));

            $user = $userCollection->get()->findOne(['mail' => $mail]);

            $response = $response->withStatus(401);
            if($user == null) {
                $userCollection->get()->insertOne(
                    [
                        'name' => $name,
                        'mail' => $mail,
                        'password' => $password,
                    ]
                );
                $response = $response->withStatus(200);
            }

            return $response;
        }

    }
}