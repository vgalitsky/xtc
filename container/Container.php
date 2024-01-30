<?php
namespace XTC\Container;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use XTC\Container\Exception\InvalidArgumentException;
use XTC\Config\ConfigInterface;
use XTC\Container\Exception\ContainerException;
use XTC\Container\Exception\ServiceAlreadyRegisteredException;
use XTC\Container\Exception\UnableToCreateServiceException;
use XTC\Debug\DebuggerInterface;

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
     * @var LoggerInterface|null The debug logger
     */
    protected ?LoggerInterface $logger = null;

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
    public function __construct(ConfigInterface $config, LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger ?? new NullLogger();

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
                throw new ServiceAlreadyRegisteredException(sprintf(_('The service "%s" was already registered'), $id_or_class));
        }
        
        /**
         * @var string $preference
         */
        $preference = $this->resolvePreference($id_or_class);

        if (!class_exists($preference)) {
            throw new \Exception(sprintf(_('Class "%s" does not exists'), $preference));
        }

        if (null === $reflection = new \ReflectionClass($preference)) {
            throw new InvalidArgumentException(sprintf(_('Unable to get a reflection for the class "%s"'), $id_or_class));
        }

        /**
         * @var ReflectionMethod|null $constructor
         */
        $constructor = $reflection->getConstructor();

        if (null === $constructor) {
            try {
                return $reflection->newInstanceWithoutConstructor();
            } catch (\ReflectionException $e) {
                throw new InvalidArgumentException(sprintf(_('Unable to create an instance for the class "%s"'), $id_or_class), $e->getCode(), $e);
            }
        }

        /**
         * @var \ReflectionParameter[] $parameters
         */
        $parameters = $constructor->getParameters();

        if (empty($parameters)) {
            try {
                $service =  $reflection->newInstanceWithoutConstructor();
            } catch (\ReflectionException $e) {
                throw new InvalidArgumentException(sprintf(_('Unable to create an instance for the class "%s" using constructor'), $id_or_class), $e->getCode(), $e);
            }
        }

        $arguments = $this->resolveArguments($parameters);
        $args = $arguments + $args;

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
        throw new InvalidArgumentException(sprintf(_('Service "%s" not found'), $id));
        
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
     * Resolve the preference based on configuration
     *
     * @param string $id
     * 
     * @return string
     */
    protected function resolvePreference(string $id): string
    {
        
        /**
         * @var string $preference
         */
        $preference = $this->config->get('preference.' . $id . '.class');

        return  (null !== $preference) ? $preference : $id;
    }

    /**
     * Resolve the arguments
     *
     * @param \ReflectionParameter[] $arguments
     * 
     * @return object[]
     */
    protected function resolveArguments(array $arguments): array
    {
        $args = [];
        /**
         * @var \ReflectionParameter $argument
         */
        foreach ($arguments as $argument) {
            $typeName = $argument->getType()->getName();

            //@TODO:VG enough???
            $is_scalar = function_exists('is_' . $typeName);

            if (!$is_scalar) {
                $args[] = $this->get($typeName, true);
            }
        }
        return $args;
    }

    /**
     * Reset the container instance
     *
     * @return void
     */
    public function reset()
    {
        $this->config = null;
        $this->container = [];
        static::$instance = $this;;
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
            return new $class(...$args);
        } catch (\Throwable $e) {
            throw new UnableToCreateServiceException(sprintf(_('Unable to create service "%s": %s'), $class, $e->getMessage()), $e->getCode(), $e);
        }
    }
    
}