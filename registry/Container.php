<?php
namespace XTC\Container;

//use Psr\Container\ContainerInterface;

/**
 * PSR simple container
 */
class Container //implements ContainerInterface
{
    /**
     * The service container
     *
     * @var array
     */
    static private array $container = [];

    /**
     * Register a service
     *
     * @param string $key
     * @param mixed $service
     * 
     * @return void
     */
    static public function register(string $key, $service): void
    {
        static::$container[$key] = $service; 
    }
    
    /**
     * Get the service
     *
     * @param string $key
     * @param mixed $default
     * 
     * @return void
     */
    static public function get(string $key, $default = null)
    {
        if (static::has($key)) {
            return static::$container[$key];
        }
        return $default;
    }

    /**
     * Check a service is registered
     *
     * @param string $key
     * 
     * @return boolean
     */
    static public function has(string $key): bool
    {
        return array_key_exists($key, static::$container) ? true : false;
    }
}