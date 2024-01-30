<?php
namespace XTC\Container;

interface FactoryInterface
{
    
    function create(string $serviceId, ...$args);
}