<?php
namespace XTC\App;

use XTC\App\Exception\BootstrapException;
use XTC\Config\Config;
use XTC\Config\ConfigInterface;
use XTC\Container\ContainerInterface;
use XTC\Container\FactoryInterface;

class Bootstrap implements BootstrapInterface
{

    const CONFIG_PATH_BASE_PATH = 'bootstrap.path.base';
    const CONFIG_PATH_CONFIG_CLASS = 'bootstrap.config.class';
    const CONFIG_PATH_CONFIG_FILES = 'bootstrap.config.files';
    const CONFIG_PATH_CONFIG_IGNORE_ERRORS = 'bootstrap.config.ignore-errors';
    const CONFIG_PATH_CONTAINER_CLASS = 'bootstrap.container.class';
    const CONFIG_PATH_CONTAINER_CONFIG_PATH = 'bootstrap.container.config-path';
    const CONFIG_PATH_FACTORY_CLASS = 'bootstrap.factory.class';
    const CONFIG_PATH_APP_CLASS = 'bootstrap.app.class';
    const CONFIG_PATH_APP_CONFIG_PATH = 'bootstrap.app.config-path';

    /**
     * The App instance
     *
     * @var AppInterface|null
     */
    protected ?AppInterface $app = null;

    /**
     * The service container instance
     *
     * @var ContainerInterface
     */
    protected ?ContainerInterface $container = null;

    /**
     * The factory instance
     *
     * @var FactoryInterface|null
     */
    protected ?FactoryInterface $factory = null;
    
    /**
     * Self singleton
     *
     * @var BootstrapInterface
     */
    static protected ?BootstrapInterface $instance = null;

    /**
     * Initial config
     *
     * @var ConfigInterface|null
     */
    protected ?ConfigInterface $config = null;

    /**
     * Initial array with basic configuration information
     *
     * @var array
     */
    protected array $initaialConfigArray = [];

    /**
     * The constructor
     *
     * @param ConfigInterface $config
     */
    public function __construct(array $config, string $base = '')
    {
        if (!is_array($config) || !array_key_exists('bootstrap', $config)) {
            throw new BootstrapException(_('Boostrap configuration must be an array'));
        }

        
        //@TODO:VG
        if (!array_key_exists('path', $config['bootstrap']) || !array_key_exists('path', $config['bootsrap'])) {
            if (empty($config['bootstrap']['path']['base'])) {
                $config['bootstrap']['path'] = [
                    'base' => defined('XTC_BASE_PATH') ? XTC_BASE_PATH : '',
                ];
            }
        }

        $this->config = new Config($config);

        self::$instance = $this;

        $this->init();
    }

    /**
     * {@inheritDoc}
     */
    static public function getInstance(): BootstrapInterface
    {
        if (!self::$instance instanceof BootstrapInterface) {
            throw new \Exception(_('Bootstrap was not initialized'));
        }
        return self::$instance;
    }

    /**
     * @return object
     */
    public static function getApp(): App
    {
        $bootstrap = self::getInstance();
        return $bootstrap->app;
    }

    /**
     * @param string|null $path The relative path to convert to absolute
     * @return string
     */
    public static function getBasePath(string $path = null): string
    {
        $basePath = rtrim(
            self::getInstance()
                ->config
                ->get(self::CONFIG_PATH_BASE_PATH), '/'
        );
        if (null !== $path) {
            $basePath .=  '/'. ltrim($path, '/');
        }
        return $basePath;
    }
    
    /**
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface
    {
        return self::getInstance()->container;
    }
    
    /**
     * @return FactoryInterface
     */
    public static function getFactory(): FactoryInterface
    {
        return self::getInstance()->factory;
    }

    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        $this->initConfig();
        $this->initContainer();
        $this->initApp();
    }

    /**
     * Init configuration
     * Check "bootstrap.config.files" path in bootstrap.json 
     *  for the files list
     * 
     * @return void
     * 
     * @throws BootstrapException
     */
    protected function initConfig(): void
    {
        $configClass = $this->config->get(self::CONFIG_PATH_CONFIG_CLASS);

        if (!class_exists($configClass)) {
            throw new BootstrapException(sprintf(_('Config class "%s" does not exist'), $configClass));
        }

        $files = $this->config->get(self::CONFIG_PATH_CONFIG_FILES);

        if (!is_array($files)) {
            throw new BootstrapException(sprintf(_('Can not find config files entry in bootstrap configuration. node "bootstrap.config.files". Please check the "bootstrap.json" file')));
        }
        
        $configArray = $this->config->all();
        
        foreach ($files as $file) {

            try {
                //@TODO:VG dataprovider
                $fileArray = json_decode(@file_get_contents(XTC_BASE_PATH . $file), true, 512, JSON_THROW_ON_ERROR);
            
            } catch (\Throwable $e) {
                if (!$this->config->get(self::CONFIG_PATH_CONFIG_IGNORE_ERRORS)) {
                    throw new BootstrapException(sprintf(_('JSON is not valid in the file "%s" or file does not exists'), XTC_BASE_PATH . $file));
                }
            }

            /**
             * Merge the configurations recursively
             * Last loaded configuration will replace existing entires
             */
            $configArray = array_replace_recursive(
                $configArray,
                $fileArray
            );
        }
        $this->config = new Config($configArray);
    }

    /**
     * Init the service container
     *
     * @return void
     */
    protected function initContainer(): void
    {
        $containerClass = $this->config->get(self::CONFIG_PATH_CONTAINER_CLASS);
        $this->container = new $containerClass(
            $this->config->get(
                $this->config->get(self::CONFIG_PATH_CONTAINER_CONFIG_PATH),
                true
            )
        );

        /**
         * Init factory as well
         */
        $factoryClass = $this->config->get(self::CONFIG_PATH_FACTORY_CLASS);
        $this->factory = new $factoryClass($this->container);
    }

    /**
     * Init the application
     *
     * @return void
     */
    protected function initApp(): void
    {
        $appClass = $this->config->get(self::CONFIG_PATH_APP_CLASS);
        $this->app = new $appClass(
            $this,
            $this->config->get(
                $this->config->get(self::CONFIG_PATH_APP_CONFIG_PATH),
                true
            ),
            $this->container,
            $this->factory
        );
    }

}