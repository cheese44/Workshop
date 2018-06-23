<?php

namespace Cheese44\Workshop {

    use MongoDB\BSON\ObjectId;
    use Psr\Container\ContainerInterface;
    use Slim\Http\Request;
    use Slim\Http\Response;

    class Workshop {
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
        public function vote(Request $request, Response $response, $args): Response {
            $userId = new ObjectId($this->container->jwt['userId']);
            $workshopId = new ObjectId($args['id']);
            $topicIds = (array)$request->getParsedBody()['topicIds'];

            $topicIds = $this->getTopicService()->mapToObjectIds($topicIds);

            $workshopCollection = new \Cheese44\Workshop\Model\Workshop($this->container->get('mongodb'));
            $workshopCollection->get()->updateOne(
                [
                    '_id' => $workshopId,
                    'topics.topicId' => ['$in' => $topicIds]
                ],
                [
                    '$addToSet' => ['topics.$[].votes' => $userId]
                ]
            );
            $workshopCollection->get()->updateOne(
                [
                    '_id' => $workshopId,
                    'topics.topicId' => ['$nin' => $topicIds]
                ],
                [
                    '$pull' => ['topics.$[].votes' => $userId]
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
        public function participate(Request $request, Response $response, $args): Response {
            $userId = new ObjectId($this->container->jwt['userId']);
            $workshopId = new ObjectId($args['id']);

            $workshopCollection = new \Cheese44\Workshop\Model\Workshop($this->container->get('mongodb'));
            $workshopCollection->get()->updateOne(
                [
                    '_id' => $workshopId
                ],
                [
                    '$addToSet' => [
                        'participantIds' => $userId
                    ]
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
        public function revokeParticipation(Request $request, Response $response, $args): Response {
            $userId = new ObjectId($this->container->jwt['userId']);
            $workshopId = new ObjectId($args['id']);

            $workshopCollection = new \Cheese44\Workshop\Model\Workshop($this->container->get('mongodb'));
            $workshopCollection->get()->updateOne(
                [
                    '_id' => $workshopId
                ],
                [
                    '$pull' => [
                        'participantIds' => $userId
                    ]
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
            $id = $args['id'];
            $workshop = $this->getWorkshopById($id);

            $response = $response->withJson($workshop);

            return $response;
        }

        /**
         * @param Request  $request
         * @param Response $response
         * @param          $args
         *
         * @return Response
         */
        public function topics(Request $request, Response $response, $args): Response {
            $id = $args['id'];
            $workshop = $this->getWorkshopById($id);

            $topicIDs = [];
            foreach ($workshop['topics'] as $topic) {
                /** @var ObjectId $idObject */
                $topicIDs[] = (string)$topic['topicId'];
            }

            $topicService = $this->getTopicService();

            $topics = $topicService->getTopicsByIds($topicIDs);

            $response = $response->withJson($topics);

            return $response;
        }

        /**
         * @param Request  $request
         * @param Response $response
         * @param          $args
         *
         * @return Response
         */
        public function upcoming(Request $request, Response $response, $args): Response {

            $workshopCollection = new \Cheese44\Workshop\Model\Workshop($this->container->get('mongodb'));

            $today = mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1);

            $cursor = $workshopCollection->get()->find(
                [
                    'date' => ['$gte' => $today]
                ],
                [
                    'limit' => 5,
                    'sort' => ['date' => 1]
                ]
            );

            $response = $response->withJson($cursor->toArray());

            return $response;
        }

        /**
         * @param string $id
         *
         * @return array|null|object
         */
        public function getWorkshopById(string $id) {
            $workshopCollection = new \Cheese44\Workshop\Model\Workshop($this->container->get('mongodb'));
            $workshop = $workshopCollection->get()->findOne(
                ['_id' => new ObjectId($id)]
            );

            return $workshop;
        }

        /**
         * @return \Cheese44\Workshop\Service\Topic
         */
        public function getTopicService(): \Cheese44\Workshop\Service\Topic {
            $topicService = $this->container->get('service_topic');

            return $topicService;
        }

    }
}