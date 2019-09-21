<?php

namespace Dnesst;

class Node{
    public $name;
    public $attributes = []; //list of Attributes
    public $children = []; //list of Nodes

    function __construct(string $name){
        $this->name = $name;
    }
}
