<?php
namespace XTC\Container;
use XTC\Container\Exception\InvalidArgumentException;

//use Psr\Container\ContainerInterface;

/**
 * PSR simple container
 */
class Container //implements ContainerInterface
{

    static protected bool $throwable = true;
    /**
     * The service container
     *
     * @var array
     */
    static protected array $container = [];

    /**
     * Undocumented function
     *
     * @param boolean $throwable
     * @return void
     */
    static public function setThrowable(bool $throwable): void
    {
        static::$throwable = $throwable;
    }

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
        if (!array_key_exists($key, static::$container)) {
            if (static::$throwable) {
                throw new InvalidArgumentException(sprintf(_('Service "%s" does not exist.'), $key));
            }
            return false;
        }
        return true;
    }


    //--------------------------------------------------------------
    public static function preAutoloadDump($event): void
    {
        return; //@TODO
        $optimize = $event->getFlags()['optimize'] ?? false;
        $rootPackage = $event->getComposer()->getPackage();

        $dir = __DIR__ . '/../lib'; // for example

        $autoloadDefinition = $rootPackage->getAutoload();
        $optimize
            ? self::writeStaticAutoloader($dir)
            : self::writeDynamicAutoloader($dir);
        $autoloadDefinition['files'][] = "$dir/autoload.php";
        $rootPackage->setAutoload($autoloadDefinition);
    }

    /**
     * Here we generate a relatively efficient file directly loading all
     * the php files we want/found. glob() could be replaced with a better
     * performing alternative or a recursive one.
     */
    private static function writeStaticAutoloader($dir): void
    {
        file_put_contents(
            "$dir/autoload.php",
            "<?php\n" . 
                implode("\n", array_map(static function ($file) {
                        return 'include_once(' . var_export($file, true) . ');';
                    }, glob("$dir/*.php"))
                )
        );
    }

    /**
     * Here we generate an always-up-to-date, but slightly slower version.
     */
    private static function writeDynamicAutoloader($dir): void
    {
        file_put_contents(
            "$dir/autoload.php",
            "<?php\n\nforeach (glob(__DIR__ . '/*.php') as \$file)\n 
            include_once(\$file);"
        );
    }
}