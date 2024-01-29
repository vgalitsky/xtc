<?php
namespace XTC\App;

use PharIo\Manifest\Application;
use XTC\Container\ContainerInterface;
//use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Log\LoggerInterface;
use XTC\App\Event\EventAppStart;
use XTC\App\Exception\AppException;
use XTC\Config\ConfigInterface;
use XTC\Debug\Debug;

class App implements AppInterface
{
    use AppTrait;

    /**
     * @var ContainerInterface|null The PSR Container
     */
    protected ?ContainerInterface $container = null;

    /**
     * @var ConfigInterface|null The Application config
     */
    protected ?ConfigInterface $config = null;

    /** 
     * @var ListenerProviderInterface $listenerProvider The listener provider 
    */
    protected ?ListenerProviderInterface $listenerProvider = null;

    /** 
     * @var EventDispatcherInterface $eventDispatcher The event dispatcher 
    */
    protected ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * @var LoggerInterface|null The logger
     */
    protected ?LoggerInterface $logger;

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
     * @param ContainerInterface $container The container
     * @param ConfigInterface    $config    The configuration
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
     * Init the application
     *
     * @return void
     */
    public function init(): void
    {
        $this->initLogger();
        $this->initEventDispatcher();

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

            if (!$this->eventDispatcher instanceof ListenerProviderInterface) {
                throw new AppException(_('Failed assert the config'));
            }

            if (!$this->config instanceof ConfigInterface) {
                throw new AppException(_('Failed assert the config'));
            }
        } catch (AppException $e) {
            if (true === self::getConfig('app.debug.enabled')) {

            }
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
            ->get(ListenerProviderInterface::class, true, true);
        
        $this->eventDispatcher = $this->container
            ->get(EventDispatcherInterface::class, true, true, $this->listenerProvider);

        /****************************************
         * Dispatch very first event
         * 
         * Create a test event
         */
        $event = $this->container->get(EventAppStart::class, true, true, ['message'=>'App init event dispatcher done']);

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
     * @return void
     */
    static public function getBasePath()
    {
        return self::getInstance()->bootstrap->getBasePath();
    }

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