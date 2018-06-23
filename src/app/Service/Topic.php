<?php

namespace Cheese44\Workshop\Service {


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
         * @param string[] $ids
         *
         * @return array
         */
        public function getTopicsByIds(array $ids) {
            $topicCollection = new \Cheese44\Workshop\Model\Topic($this->container->get('mongodb'));

            $ids = $this->mapToObjectIds($ids);

            $topics = $topicCollection->get()->find(
                [
                    '_id' => ['$in' => $ids]
                ]
            );

            return $topics->toArray();
        }

        /**
         * @param array $ids
         *
         * @return array
         */
        public function mapToObjectIds(array $ids): array {
            $ids = array_map(function ($id) {
                return new ObjectId($id);
            }, $ids);
            return $ids;
        }

    }
}