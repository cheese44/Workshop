<?php

namespace Cheese44\Workshop\Model {

    class Topic extends Collection {


        /**
         * @return \MongoDB\Collection
         */
        public function get() {
            return $this->database->selectCollection('topic');
        }
    }
}