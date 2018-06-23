<?php

namespace Cheese44\Workshop\Model {

    class Workshop extends Collection {


        /**
         * @return \MongoDB\Collection
         */
        public function get() {
            return $this->database->selectCollection('workshop');
        }
    }
}