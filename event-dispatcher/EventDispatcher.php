<?php
namespace XTC\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{

    /**
     * @var ListenerProviderInterface The listeners
     */
    protected ?ListenerProviderInterface $listeners = null;

    /**
     * The constructor
     *
     * @param ListenerProviderInterface $listeners
     * 
     * @return void
     */
    public function __construct(ListenerProviderInterface $listeners) 
    {
        $this->listeners = $listeners;
    }
    
    /**
     * Dispatch the event
     *
     * @param object $event
     * 
     * @return void
     */
    public function dispatch(object $event)
    {
        
        foreach ($this->listeners->getListenersForEvent($event) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }
            //@TODO:VG try-catch
            //try {
                //call_user_func_array($listener, [$event]); //php7.4 compability
                $listener($event);
            //} 
        }
    }

}