<?php

namespace Cheese44\Workshop {

    use MongoDB\BSON\ObjectId;
    use Psr\Container\ContainerInterface;
    use Slim\Http\Request;
    use Slim\Http\Response;

    class Topic {
        /** @var ContainerInterface $container */
        private $container;

        // constructor receives container instance
        public function __construct(ContainerInterface $container) {
            $this->container = $container;
        }

        /**
         * @param Request  $request
         * @param Response $response
         * @param          $args
         *
         * @return Response
         */
        public function save(Request $request, Response $response, $args): Response {
            $userId = new ObjectId($this->container->jwt['userId']);

            $topicCollection = new \Cheese44\Workshop\Model\Topic($this->container->get('mongodb'));
            $topicCollection->get()->insertOne(
                [
                    'title' => (string)$request->getParsedBody()['title'],
                    'text' => (string)$request->getParsedBody()['text'],
                    'owner' => $userId
                ]
            );

            return $response;
        }

        /**
         * @param Request  $request
         * @param Response $response
         * @param          $args
         *
         * @return Response
         */
        public function detail(Request $request, Response $response, $args): Response {
            $id = new ObjectId($args['id']);

            $topicCollection = new \Cheese44\Workshop\Model\Topic($this->container->get('mongodb'));
            $topic = $topicCollection->get()->findOne(['_id' => $id]);

            $response = $response->withJson($topic);

            return $response;
        }

        /**
         * @param Request  $request
         * @param Response $response
         * @param          $args
         *
         * @return Response
         */
        public function outstanding(Request $request, Response $response, $args): Response {

            $topicCollection = new \Cheese44\Workshop\Model\Topic($this->container->get('mongodb'));

            $cursor = $topicCollection->get()->find(
                [
                    '$or' => [
                        ['held' => ['$exists' => false]],
                        ['held' => false],
                    ]
                ],
                [
                    'sort' => ['title' => 1]
                ]
            );

            $response = $response->withJson($cursor->toArray());

            return $response;
        }

    }
}