<?php
namespace XTC\Container\Factory;


class Factory
{
    static public function containerFactory(string $id_or_class, ...$args)
    {
        return function (ContainerInterface $container) use ($id_or_class, $args) {
            return $container->create($id_or_class, ...$args);
        };
    }

    static public function factory(string $class, ...$args)
    {
        return function () use ($class, $args) {
            return new ($class)(...$args);
        };
    }
}