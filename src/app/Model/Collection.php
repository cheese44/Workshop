<?php

namespace Cheese44\Workshop\Model {

    use MongoDB\Database;
    use Psr\Container\ContainerInterface;

    abstract class Collection {

        /** @var Database $database */
        protected $database;

        /**
         * Collection constructor.
         *
         * @param Database $database
         */
        public function __construct(Database $database) {
            $this->database = $database;
        }

        /**
         * @return \MongoDB\Collection
         */
        abstract public function get();
    }
}