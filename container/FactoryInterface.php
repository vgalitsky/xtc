<?php
namespace XTC\Container;

interface FactoryInterface
{
    
    function create(...$args);
}