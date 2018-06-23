<?php

namespace Cheese44\Workshop\Model {

    class User extends Collection {


        /**
         * @return \MongoDB\Collection
         */
        public function get() {
            return $this->database->selectCollection('user');
        }
    }
}