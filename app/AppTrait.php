<?php
namespace XTC\App;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

trait AppTrait
{
    /**
     * Get the container instance
     *
     * @return ContainerInterface
     */
    public static function getContainer(): ContainerInterface
    {
        return self::getInstance()->container;
    }
    
    /**
     * Get the event-dispacther instance
     *
     * @return EventDispatcherInterface
     */
    static public function getEventDispatcher(): EventDispatcherInterface
    {
        return self::getInstance()->eventDispatcher;
    }
    
    /**
     * Get the event-dispacther instance
     *
     * @return ListenerProviderInterface
     */
    static public function getListenerProvider(): ListenerProviderInterface
    {
        return self::getInstance()->listenerProvider;
    }

}