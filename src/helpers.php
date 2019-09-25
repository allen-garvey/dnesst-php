<?php

function trimValue($str){
    return preg_replace("/^\\s+|\\s+$/", '', $str);
}