<?php

define('MODELS_PATH', dirname(__FILE__).'/models/');

require_once MODELS_PATH.'node.php';
require_once MODELS_PATH.'attribute.php';
require_once MODELS_PATH.'parser-state.php';
require_once  dirname(__FILE__).'/helpers.php';

//do input filename validation here
$inputFileName = './tests/test1.scss';

//setup parser initial state
use Dnesst\ParserState as ParserState;
use Dnesst\Node as Node;
use Dnesst\Attribute as Attribute;

$parserState = new ParserState;
$rootNode = new Node('');
$nodeStack = [$rootNode]; //used to keep track of how nested the current node tree is
$currentValue = ''; //stores either current node/attribute name, or attribute value
$previousChar = '';
$currentAttribute = null;

//iterate over each character in string
//required to use preg_split to get each character in unicode string
//https://stackoverflow.com/questions/1293950/php-split-a-string-in-to-an-array-foreach-char
foreach (preg_split('//u', file_get_contents($inputFileName), null, PREG_SPLIT_NO_EMPTY) as $char) {
    if($parserState->quoteState != ParserState::QUOTE_STATE_NONE && !($parserState->quoteState == ParserState::QUOTE_STATE_SINGLE_QUOTES && $char == "'") && !($parserState->quoteState == ParserState::QUOTE_STATE_DOUBLE_QUOTES && $char == '"')){
        $currentValue .= $char;
        $previousChar = '';
        continue;
    }

    switch($char){
        case '"':
            if($parserState->quoteState == ParserState::QUOTE_STATE_DOUBLE_QUOTES){
                $parserState->quoteState = ParserState::QUOTE_STATE_NONE;
            }
            else{
                $parserState->quoteState = ParserState::QUOTE_STATE_DOUBLE_QUOTES;
            }
            break;
        case "'":
            if($parserState->quoteState == ParserState::QUOTE_STATE_SINGLE_QUOTES){
                $parserState->quoteState = ParserState::QUOTE_STATE_NONE;
            }
            else{
                $parserState->quoteState = ParserState::QUOTE_STATE_SINGLE_QUOTES;
            }
            break;
        case '{':
            $parent = end($nodeStack);
            $currentNode = parseNode($currentValue, $parent);
            $parent->children[] = $currentNode;
            $nodeStack[] = $currentNode;
            $currentValue = '';
            $previousChar = '';
            continue 2; //continue foreach https://www.php.net/manual/en/control-structures.continue.php
        case '}':
            array_pop($nodeStack);
            $previousChar = '';
            continue 2; //continue foreach https://www.php.net/manual/en/control-structures.continue.php
        case ':':
            $parserState->isInAttributeValue = true;
            $currentAttribute = new Attribute(trimValue($currentValue), '');
            $currentValue = '';
            $previousChar = '';
            continue 2; //continue foreach https://www.php.net/manual/en/control-structures.continue.php
        case ';':
            $parserState->isInAttributeValue = false;
            $currentAttribute->value = trimValue($currentValue);
            $currentValue = '';
            $previousChar = '';
            end($nodeStack)->attributes[] = $currentAttribute;
            $currentAttribute = null;
            continue 2; //continue foreach https://www.php.net/manual/en/control-structures.continue.php
    }

    if($char != ' ' || $previousChar != ' '){
        $currentValue .= $char;
    }
    $previousChar = $char;
}


var_dump($rootNode);