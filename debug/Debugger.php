<?php

namespace XTC\Debug;

use Psr\Log\LoggerInterface;
use XTC\App\App;
use XTC\App\Bootstrap;
use XTC\Container\ContainerInterface;
use XTC\Debug\Counter\CounterInterface;
use XTC\Debug\Timer\TimerInterface;

class Debugger implements DebuggerInterface
{
    protected ?string $id = '';
    protected ?array $debuggers = [];
    protected ContainerInterface $container;
    protected ?CounterInterface $counter = null;
    protected ?TimerInterface $timer = null;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Create the debugger
     *
     * @param string $id
     * 
     * @return array
     */
    public function create(string $id): array
    {
        return $this->reset($id);
    }

    /**
     * Get the debugger 
     *
     * @param string $id
     * @param string $type
     * 
     * @return void
     */
    public function get(string $id, string $type = '')
    {
        if (!array_key_exists($id, $this->debuggers)) {
            return null;
        }
        if (empty($type)) {
            return $this->debuggers[$id];
        }
        return $this->debuggers[$id][$type];
    }

    /**
     * Reset (Create) the debugger
     *
     * @param string $id
     * 
     * @return array
     */
    public function reset(string $id): array
    {
        $debugger = [
            'counter' => $this->container->create(CounterInterface::class),
            'timer' => $this->container->create(TimerInterface::class),
            'logger' => $this->container->create(
                LoggerInterface::class,
                Bootstrap::getBasePath() . '/log/' . $id . '-debugger.log'
            ),
        ];

        $this->debuggers[$id] = $debugger;

        return $debugger;
    }
}