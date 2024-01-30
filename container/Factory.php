<?php
namespace XTC\Container;

use Psr\Container\ContainerInterface;
use XTC\Container\ContainerInterface as XTCContainerInterface;

class Factory implements FactoryInterface
{

    protected ?XTCContainerInterface $container = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Create the instance
     *
     * @param string $serviceId The service Id
     * @param mixed  ...$args   The service arguments
     * 
     * @return object
     */
    function create(string $serviceId, ...$args): object
    {
        return $this->container->create($serviceId, ...$args);
    }

    /**
     * Lazy
     *
     * @param string $serviceId
     * @param mixed ...$args
     * 
     * @return callable
     */
    function lazy(string $serviceId, ...$args)
    {
        return function() use ($serviceId, $args) {
            return $this->create($serviceId, ...$args);
        };
    }
}