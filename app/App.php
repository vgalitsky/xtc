<?php
namespace XTC\App;


use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use XTC\Container\ContainerInterface;
use XTC\EventDispatcher\ListenerProviderInterface as XTCListenerProviderInterface;
use XTC\App\Event\EventAppStart;
use XTC\App\Exception\AppException;
use XTC\Cache\NullCache;
use XTC\Config\ConfigInterface;

class App implements AppInterface
{
    use AppTrait;
    use AppDebugTrait;

    /**
     * @var ContainerInterface|null The PSR Container
     */
    protected ?ContainerInterface $container = null;

    /**
     * @var ConfigInterface|null The Application config
     */
    protected ?ConfigInterface $config = null;

    /** 
     * @var XTCListenerProviderInterface The listener provider 
    */
    protected ?XTCListenerProviderInterface $listenerProvider = null;

    /** 
     * @var EventDispatcherInterface The event dispatcher 
    */
    protected ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * @var LoggerInterface|null The logger
     */
    protected ?LoggerInterface $logger = null;
    
    /**
     * @var CacheInterface|null The cache
     */
    protected ?CacheInterface $cache = null;

    /**
     * @var Bootstrap|null
     */
    protected ?Bootstrap $bootstrap = null;

    /**
     * @var App|null The self instance for static methods
     */
    static protected ?App $instance = null;



    /**
     * The constructor
     *
     * @param BootstrapInterface $bootstrap The bootstrap
     * @param ConfigInterface    $config    The configuration
     * @param ContainerInterface $container The container
     * 
     */
    public function __construct(
        BootstrapInterface $bootstrap,
        ConfigInterface $config,
        ContainerInterface $container
    ) {

        self::$instance = $this;

        $this->bootstrap = $bootstrap;
        $this->container = $container;
        $this->config = $config;
       
        $this->init();
    }

    /**
     * Get self instance static
     *
     * @return App
     */
    public static function getInstance(): App
    {
        return self::$instance;
    }

    /**
     * Get Bootstrap instance
     *
     * @return void
     */
    static public function getBootstrap(): Bootstrap
    {
        return self::getInstance()->bootstrap;
    }

    /**
     * Init the application
     *
     * @return void
     */
    public function init(): void
    {
        $this->initLogger();
        $this->initCache();
        $this->initEventDispatcher();
        
        if (self::getConfig('app.debug.enabled')) {
            $this->initDebug();
        }

        $this->assert();
    }

    /**
     * Assert App initialization
     *
     * @return void
     */
    protected function assert(): void
    {
        try {
            if (!$this->container instanceof ContainerInterface) {
                throw new AppException(_('Failed assert the Container'));
            }
            
            if (!$this->eventDispatcher instanceof EventDispatcherInterface) {
                throw new AppException(_('Failed assert the Event Dispatcher'));
            }

            if (!$this->listenerProvider instanceof ListenerProviderInterface) {
                throw new AppException(_('Failed assert the ListenerProvider'));
            }

            if (!$this->config instanceof ConfigInterface) {
                throw new AppException(_('Failed assert the Config'));
            }
            
            if (!$this->cache instanceof CacheInterface) {
                throw new AppException(_('Failed assert the Cache'));
            }
          
        } catch (AppException $e) {
            if (true === self::getConfig('app.debug.enabled')) {
                
            }
        }
    }

    /**
     * The cache 
     * @return void
     */
    protected function initCache()
    {
        if (self::getConfig('cache.enabled')) {
            $this->cache = $this->container->get(CacheInterface::class,  self::getConfig('cache.path'));
        } else {
            $this->cache = new NullCache();
        }
    }

    /**
     * @return void The app logger (debug)
     */
    protected function initLogger()
    {
        $this->logger = $this->container->create(LoggerInterface::class, XTC_BASE_PATH .'/'. self::getConfig('app.debug.log'));
    }

    /**
     * Init the event dispatcher instance
     *
     * @return void
     */
    protected function initEventDispatcher()
    {
       
        $this->listenerProvider = $this->container
            ->get(ListenerProviderInterface::class);
        
        $this->eventDispatcher = $this->container
            ->get(EventDispatcherInterface::class, $this->listenerProvider);

        /****************************************
         * Dispatch very first event
         * 
         * Create a test event
         */
        $event = $this->container->get(EventAppStart::class, ['message'=>'App init event dispatcher done']);

        /**
         * Attach it to the listener provider
         */
        $this->listenerProvider->attach(EventAppStart::class, [self::class, 'onStartStatic']);
        $this->listenerProvider->attach(EventAppStart::class, [$this, 'onStart']);

//Debug::dump($this->listenerProvider);
        /**
         * Dispatch it
         */
        $this->eventDispatcher->dispatch($event);
        /**
         ****************************************/
    }

    /**
     * Get the config instance
     *
     * @return mixed|ConfigInterface
     */
    static public function getConfig(string $path, bool $subNodeAsInstance = false)
    {
        return self::getInstance()->config->get($path, $subNodeAsInstance);
    }


    /**
     * @param string|null $path
     * 
     * @return string
     */
    static public function getBasePath(string $path = null): string
    {
        return self::getInstance()->bootstrap->getBasePath($path);
    }

    /**
     * Get the cache
     *
     * @return CacheInterface
     */
    static public function getCache(): CacheInterface
    {
        return self::getInstance()->cache;
    }


//-Test events---------------------------------------------------
    /**
     * The event listener method
     *
     * @param Event $event
     * 
     * @return void
     */
    static public function onStartStatic(object $event): void
    {
        if (true == self::getConfig('app.debug.enabled')) {
            self::getInstance()->logger->debug(
                _('STATIC: The event dispatching is started successfully with event "{event_class}"'),
                ['event_class' => get_class($event)]
            );
        }
    }

    /**
     * On start non static self tests
     *
     * @param [type] $event
     * @return void
     */
    public function onStart($event) 
    {
        if (true == self::getConfig('app.debug.enabled')) {
            self::getInstance()->logger->debug(
                _('NON STATIC: The event dispatching is started successfully with event "{event_class}"'),
                ['event_class' => get_class($event)]
            );
        }
    }


}