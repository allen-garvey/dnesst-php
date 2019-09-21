<?php

namespace Dnesst;

class Attribute{
    public $name;
    public $value;

    function __construct(string $name, string $value){
        $this->name = $name;
        $this->value = $value;
    }
    
}
