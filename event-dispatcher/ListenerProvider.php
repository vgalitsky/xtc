<?php
namespace XTC\EventDispatcher;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * Undocumented variable
     *
     * @var array<string, <string, callable> The callable listener
     */
    protected array $listeners = [];

    static int $uniq = 9999;

    /**
     * {@inheritDoc}
     */
    public function attach(string $type, $listener, int $priority = 0): void
    {
        //@TODO:VG validate listener
        if (!array_key_exists($type, $this->listeners)) {
            $this->listeners[$type] = [];
        }
        $priorityKey = (float)$priority + (float)(--static::$uniq/10000);
        $this->listeners[$type][(string)$priorityKey] = $listener;
        krsort($this->listeners[$type]);
    }

    /**
     * {@inheritDoc}
     */
    public function getListenersForEvent(object $event): iterable
    {
        $types = array_merge([get_class($event)], class_implements($event));
        
        foreach ($types as $type) {
            if (array_key_exists($type, $this->listeners)) {
                foreach ($this->listeners[$type] as $listener) {
                    yield $listener;
                }
            }
        }
    }
}