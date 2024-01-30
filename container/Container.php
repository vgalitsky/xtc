<?php
namespace XTC\Container;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use XTC\App\Bootstrap;
use XTC\Container\Exception\InvalidArgumentException;
use XTC\Config\ConfigInterface;
use XTC\Container\Exception\ContainerException;
use XTC\Container\Exception\ServiceAlreadyRegisteredException;
use XTC\Container\Exception\UnableToCreateServiceException;
use XTC\Container\Preference\Preference;
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
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
        static::$instance = $this;
        $this->logger = $this->get("LoggerFactory")
            ->create(
                LoggerInterface::class,
                Bootstrap::getBasePath('/log/xtc/container.log')
            );// ?? new NullLogger();
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
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->container);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id, ...$args): object
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
         * If not found than create
         * 
         * @throws UnableToCreateServiceException
         */
        if (null !== $service = $this->create($id, ...$args)) {
            return $service;
        }

        /**
         * Service not found
         */
        throw new InvalidArgumentException(sprintf(_('Service "%s" not found'), $id));
        
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $id_or_class, ...$args): object
    {
        /**
         * @var Preference $preference
         */
        $preference = $this->resolvePreference($id_or_class);

        if (null === $preference->getClass() || !class_exists($preference->getClass())) {
            throw new \Exception(sprintf(_('Class "%s" does not exists'), $preference->getClass()));
        }

        if (null === $reflection = new \ReflectionClass($preference->getClass())) {
            throw new InvalidArgumentException(sprintf(_('Unable to get a reflection for the class "%s"'), $id_or_class));
        }

        /**
         * @var \ReflectionMethod|null $constructor
         */
        $constructor = $reflection->getConstructor();

        if (null === $constructor) {
            try {
                $service = $reflection->newInstanceWithoutConstructor();
            } catch (\ReflectionException $e) {
                throw new InvalidArgumentException(sprintf(_('Unable to create an instance for the class "%s"'), $id_or_class), $e->getCode(), $e);
            }
        } else {

            /**
             * @var \ReflectionParameter[] $reflectionParameters
             */
            $reflectionParameters = $constructor->getParameters();

            if (empty($reflectionParameters)) {
                try {
                    /**
                     * Simple create instance
                     */
                    $service =  $reflection->newInstanceWithoutConstructor();

                } catch (\ReflectionException $e) {
                    throw new InvalidArgumentException(sprintf(_('Unable to create an instance for the class "%s" using constructor'), $id_or_class), $e->getCode(), $e);
                }
            }

            /**
             * Resolver service agruments
             * @TODO:VG merge arguments from Preference configuration
             */
            $arguments = $this->resolveArguments($reflectionParameters, ...$args);

            /**
             * Finally create the new service instance
             */
            $service = $reflection->newInstance(...$arguments);
        }

        //$service = $this->createServiceInstance($preference->getClass(), ...$arguments);

        $this->lifeCycle($preference, $service);

        return $service;
    }

    /**
     * Resolve the preference based on configuration
     *
     * @param string $id
     * 
     * @return Preference
     */
    protected function resolvePreference(string $id): Preference
    {
        $preferenceData = $this->config->get(
            static::CONFIG_PREFERENCE_PATH . '.' . $id
        );

        /**
         * Set the preference class to passed $id if preference config does not exists
         */
        $preferenceData = is_array($preferenceData) ? $preferenceData : ['class'=>$id];

        /**
         * @var Preference $preference
         */
        $preference = new Preference($id, $preferenceData);

        
        if (null !== $preference->getReference()) {
            $referrer = $preference;
            //@TODO:VG compile???
            $preference = $this->resolvePreference($preference->getReference());
            $preference->setReferrer($referrer);
        }

        return  $preference;
    }

    /**
     * Resolve the arguments
     *
     * @param \ReflectionParameter[] $parameters The reflection parameters
     * @param mixed                  ...$args The serviece arguments
     * 
     * @return mixed[]
     */
    protected function resolveArguments(array $parameters, ...$args): array
    {
        $arguments = [];
        /**
         * @var \ReflectionParameter $argument
         */
        foreach ($parameters as $argument) {
            $typeName = $argument->getType()->getName();

            //@TODO:VG enough???
            $is_scalar = function_exists('is_' . $typeName);

            if (!$is_scalar) {
                $arguments[] = $this->get($typeName);
            } else {
                //@TODO:VG check native args???
            }
        }

        /**
         * Add arguments passed via create method call 
         * Meaning these arguments came outside the container
         * And no need to be resolved
         */
        array_push($arguments, ...$args);

        return $arguments;
    }

    /**
     * Cretae a service instance
     *
     * @param string $class 
     * @param mixed  ...$args 
     * 
     * @return object
     * @throws InvalidArgumentException
     */
    protected function createServiceInstance(string $class, ...$args): object
    {
        try {
            //$factory = Factory::factory($class);
            
            return new $class(...$args);

        } catch (\Throwable $e) {
            throw new InvalidArgumentException(sprintf(_('Unable to create service "%s": %s'), $class, $e->getMessage()), $e->getCode(), $e);
        }
    }

    protected function lifeCycle(Preference $preference, object $service)
    {
        /**
         * Check referres
         */
        $finalClass = $preference->getClass();
        while ($preference->hasReferrer()) {
            $preference = $preference->getReferrer();
        }

        /**
         * Make the class alias
         */
        if ($finalClass !== $preference->getClass()) {
            class_alias($finalClass, $preference->uid());
        }

        /**
         * If singleton
         */
        if ($preference->isSingleton()) {
            $this->register($preference->uid(), $service);
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
        $this->container = [];
        static::$instance = $this;;
    }
}