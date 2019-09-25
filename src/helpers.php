<?php

define('MODELS_PATH', dirname(__FILE__).'/models/');

require_once MODELS_PATH.'node.php';
require_once MODELS_PATH.'attribute.php';
require_once MODELS_PATH.'parser-state.php';

use Dnesst\ParserState as ParserState;
use Dnesst\Node as Node;
use Dnesst\Attribute as Attribute;


function trimValue($str){
    return preg_replace("/^\\s+|\\s+$/", '', $str);
}


function parseNode(string $value, Node $parent): Node{
    $currentValue = '';
    $previousChar = '';

    $quoteState = ParserState::QUOTE_STATE_NONE;

    //iterate over each character in string
    //required to use preg_split to get each character in unicode string
    //https://stackoverflow.com/questions/1293950/php-split-a-string-in-to-an-array-foreach-char
    foreach (preg_split('//u', $value, null, PREG_SPLIT_NO_EMPTY) as $char) {
        if($quoteState != ParserState::QUOTE_STATE_NONE && !($quoteState == ParserState::QUOTE_STATE_SINGLE_QUOTES && $char == "'") && !($quoteState == ParserState::QUOTE_STATE_DOUBLE_QUOTES && $char == '"')){
            $currentValue .= $char;
            $previousChar = '';
            continue;
        }

        $charExpanded = $char;

        switch($char){
            case '"':
                if($quoteState == ParserState::QUOTE_STATE_DOUBLE_QUOTES){
                    $quoteState = ParserState::QUOTE_STATE_NONE;
                }
                else{
                    $quoteState = ParserState::QUOTE_STATE_DOUBLE_QUOTES;
                }
                break;
            case "'":
                if($quoteState == ParserState::QUOTE_STATE_SINGLE_QUOTES){
                    $quoteState = ParserState::QUOTE_STATE_NONE;
                }
                else{
                    $quoteState = ParserState::QUOTE_STATE_SINGLE_QUOTES;
                }
                break;
            case '&':
                $charExpanded = $parent->name;
                break;
        }

        if($char != ' ' || $previousChar != ' '){
            $currentValue .= $charExpanded;
        }
        $previousChar = $char;
    }

    return new Node(trimValue($currentValue));
}