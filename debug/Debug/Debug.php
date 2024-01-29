<?php
namespace XTC\Debug;

class Debug
{
    static public function dump($value)
    {
        echo '<pre>';
        print_r($value);
        echo '</pre>';
    }
}