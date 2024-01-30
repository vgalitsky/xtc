<?php
namespace XTC\Container;

use Psr\Container\ContainerInterface;
use XTC\Container\ContainerInterface as XTCContainerInterface;

class Factory implements FactoryInterface
{

    protected ?XTCContainerInterface $container = null;
    protected string $serviceId = '';
    protected array $args = [];
    public function __construct(ContainerInterface $container, string $serviceId, ...$args)
    {
        $this->container = $container;
        $this->serviceId = $serviceId;
        $this->args = $args;
    }

    function create(...$args)
    {
        return $this->container->create($this->serviceId, ...$this->args);
    }

    // static public function containerFactory(string $id_or_class, ...$args)
    // {
    //     return function (ContainerInterface $container) use ($id_or_class, $args) {
    //         return $container->create($id_or_class, ...$args);
    //     };
    // }

    // static public function factory(string $class, ...$args)
    // {
    //     return function () use ($class, $args) {
    //         return new ($class)(...$args);
    //     };
    // }
}