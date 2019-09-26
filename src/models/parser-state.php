<?php

namespace Dnesst;

class ParserState{
    public $quoteState;
    public $currentParseValue;

    const QUOTE_STATE_NONE = 0;
    const QUOTE_STATE_SINGLE_QUOTES = 1;
    const QUOTE_STATE_DOUBLE_QUOTES = 1;

    function __construct(){
        $this->quoteState = ParserState::QUOTE_STATE_NONE;
        $this->currentParseValue = '';
    }
    
}
