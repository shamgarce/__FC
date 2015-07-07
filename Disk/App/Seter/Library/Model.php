<?php

namespace Seter\Library;

class Model{

    public function __get($ModelName) {
        $ModelName = '\\'.$ModelName;
        return new $ModelName();
    }
}

