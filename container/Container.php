<?php
namespace XTC\Container;

use XTC\Container\Exception\InvalidArgumentException;
use XTC\Config\ConfigInterface;
use XTC\Container\Exception\ContainerException;
use XTC\Container\Exception\ServiceAlreadyRegisteredException;
use XTC\Container\Exception\UnableToCreateServiceException;

/**
 * PSR simple container
 */
class Container implements ContainerInterface
{
    const CONFIG_PREFERENCE_PATH = 'preference';
    /**
     * @var ConfigInterface|null Config e.g. DI configuration
     */
    protected ?ConfigInterface $config = null;

    /**
     * @var boolean
     */
    protected bool $throwable = true;

    /**
     * @var array The service container
     */
    protected array $container = [];

    /**
     * @var Container|null Self singleton
     */
    static protected ?Container $instance = null;

    /**
     * Reserved ids for self instance
     *
     * @var array
     */
    protected array $reserverdIds = [
        \Psr\Container\ContainerInterface::class,
        "ContainerInterface",
        "Container",
    ];
    /**
     * The constructor
     *
     * @param ConfigInterface $config The configuration
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        static::$instance = $this;
        
    }

    /**
     * Container singgleton
     *
     * @return ContainerInterface
     */
    static public function getInstance(): ContainerInterface
    {
        if (!static::$instance instanceof Container) {
            throw new ContainerException(_('Container was not initialized'));
        }
        return static::$instance;
    }

    /**
     * Set container can throw exceptions on not found service
     * Or return NULL if service was not found
     *
     * @param boolean $throwable 
     * 
     * @return void
     */
    public function setThrowable(bool $throwable): void
    {
        $this->throwable = $throwable;
    }


    /**
     * Sets the config
     *
     * @param ConfigInterface $config
     * 
     * @return void
     */
    public function setConfig(ConfigInterface $config): void
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function register(string $key, $service): void
    {
        $this->container[$key] = $service; 
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $id_or_class, ...$args): object
    {
        if ($this->has($id_or_class)) {
            if (true === $this->throwable) {
                throw new ServiceAlreadyRegisteredException(sprintf(_('Service "%s" already registered'), $id_or_class));
            }
        }
        
        $configPath = static::CONFIG_PREFERENCE_PATH. '.'.$id_or_class.'.class';
        $preference = $this->config->get($configPath);
        $preference = $preference ?? $id_or_class;
       
        /** @var object $service */
        $service = $this->createServiceInstance($preference, ...$args);

        return $service;
    }
    
    /**
     * Get the service
     *
     * @param string $id              The service identifier
     * @param bool   $create          Create service if needed
     * @param bool   $registerCreated If true than register created service
     * @param mixed  ...$args         Service Constructor arguments 
     * 
     * @return object|null
     */
    public function get(string $id, bool $create = false, bool $registerCreated = false,  ...$args): object
    {
        if (in_array($id, $this->reserverdIds)) {
            return static::getInstance();
        }

        /**
         * Return existing service instance
         */
        if ($this->has($id)) {
            return $this->container[$id];
        }
        
        /**
         * If not found and can register than try to do
         * 
         * @throws UnableToCreateServiceException
         */
        if (true == $create) {
            if (null !== $service = $this->create($id, ...$args)) {
                if (true === $registerCreated) {
                    $this->register($id, $service);
                }
                return $service;
            }
        }

        /**
         * Service not found
         */
        if (true === $this->throwable) {
            throw new InvalidArgumentException(sprintf(_('Service "%s" not found'), $id));
        }
        
        return null;
    }

    /**
     * Check a service is registered
     *
     * @param string $id Service identifier
     * 
     * @return boolean
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->container);
    }

    /**
     * Cretae a service instance
     *
     * @param string $class 
     * @param mixed  ...$args 
     * 
     * @return void
     * @throws UnableToCreateServiceException
     */
    protected function createServiceInstance(string $class, ...$args): object
    {
        //@TODO:VG resolve
        try {
            //$factory = Factory::factory($class);
            if (!class_exists($class)) {
                throw new \Exception(sprintf(_('Class "%s" does not exists'), $class));
            }
            return new $class(...$args);
        } catch (\Throwable $e) {
            throw new UnableToCreateServiceException(sprintf(_('Unable to create service "%s": %s'), $class, $e->getMessage()), $e->getCode(), $e);
        }
    }

    /**
     * Reset the container instance
     *
     * @return void
     */
    public function reset()
    {
        $this->config = null;

        //$this->throwable = true;

        $this->container = [];
        static::$instance = $this;;
    }
}