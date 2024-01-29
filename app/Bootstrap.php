<?php
namespace XTC\App;

use XTC\App\Exception\BootstrapException;
use XTC\Config\Config;
use XTC\Config\ConfigInterface;
use XTC\Container\ContainerInterface;

class Bootstrap implements BootstrapInterface
{
    protected ?AppInterface $app = null;
    /**
     * The service container instance
     *
     * @var ContainerInterface
     */
    protected ?ContainerInterface $container = null;
    
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

        static::$instance = $this;

        $this->init();
    }

    /**
     * {@inheritDoc}
     */
    static public function getInstance(): BootstrapInterface
    {
        if (!static::$instance instanceof BootstrapInterface) {
            throw new \Exception(_('Bootstrap was not initialized'));
        }
        return static::$instance;
    }

    /**
     * @return object
     */
    public static function app(): App
    {
        $bootstrap = static::getInstance();
        return $bootstrap->app;
    }

    /**
     * @return string
     */
    public static function getBasePath(): string
    {
        return self::getInstance()->config->get('boostrap.path.base');
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
     * @return void
     */
    protected function initConfig()
    {
        //@TOTO:VG
        $a = new class {
            public $a = 1;
        };
//-------------------------------------------------------------------
        

        $configClass = $this->config->get('bootstrap.config.class');

        if (!class_exists($configClass)) {
            throw new BootstrapException(sprintf(_('Config class "%s" does not exist'), $configClass));
        }

        $files = $this->config->get('bootstrap.config.files');
        if (!is_array($files)) {
            throw new BootstrapException(sprintf(_('Can not find config files entry in bootstrap configuration. node "bootstrap.config.files". Please check the "bootstrap.json" file')));
        }
        
        $configArray = $this->config->all();
        
        foreach ($files as $file) {

            try {
                //@TODO:VG dataprovider
                $fileArray = json_decode(@file_get_contents(XTC_BASE_PATH . $file), true, 512, JSON_THROW_ON_ERROR);
            
            } catch (\Throwable $e) {
                if (!$this->config->get('bootstrap.config.ignore-errors')) {
                    throw new BootstrapException(sprintf(_('JSON is not valid in the file "%s" or file does not exists'), XTC_BASE_PATH . $file));
                }
            }

            $configArray = array_merge_recursive(
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
    protected function initContainer()
    {
        $containerClass = $this->config->get('bootstrap.container.class');
        $this->container = new $containerClass(
            $this->config->get(
                $this->config->get('bootstrap.container.config-path'),
                true
            )
        );
    }

    /**
     * Init the application
     *
     * @return void
     */
    protected function initApp()
    {
        $appClass = $this->config->get('bootstrap.app.class');
        $this->app = new $appClass(
            $this,
            $this->config->get(
                $this->config->get('bootstrap.app.config-path'),
                true
            ),
            $this->container
        );
    }

}