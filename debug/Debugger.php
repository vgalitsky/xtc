<?php

namespace XTC\Debug;

use Psr\Log\LoggerInterface;

use XTC\App\Bootstrap;
use XTC\Container\ContainerInterface;
use XTC\Debug\Counter\CounterInterface;
use XTC\Debug\Message\MessageStackInterface;
use XTC\Debug\Timer\TimerInterface;

class Debugger implements DebuggerInterface
{
    /**
     * @var string|null
     */
    protected ?string $id = '';

    /**
     * @var ContainerInterface|null
     */
    protected ?ContainerInterface $container = null;

    /**
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger;

    /**
     * @var CounterInterface|null
     */
    protected ?CounterInterface $counter = null;

    /**
     * @var TimerInterface|null
     */
    protected ?TimerInterface $timer = null;

    /**
     * @var MessageStackInterface
     */
    protected ?MessageStackInterface $messages = null;

    /**
     * The constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container, 
        string $id
    ) {
        $this->container = $container;
        $this->messages = $this->container->create(MessageStackInterface::class);
        $this->counter = $this->container->create(CounterInterface::class);
        $this->timer = $this->container->create(TimerInterface::class);
        $this->logger = $this->container->create(
            LoggerInterface::class,
            Bootstrap::getBasePath() . '/log/' . 'debugger-' . $id . '.log'
        );
    }

    /**
     * Get the debugger Timer(s)
     *
     * @param string $timer
     * 
     * @return TimerInterface
     */
    public function getTimer(?string $timer = null): TimerInterface
    {
        return (null === $timer) ? $this->timer : $this->timer->get($timer);
    }
    
    /**
     * Get the debugger Counters
     * If the $counter is specified than return int (counter result)
     *
     * @param string $counter
     * 
     * @return CounterInterface|int
     */
    public function getCounter(?string $counter = null): CounterInterface
    {
        return $this->counter;
        //return $counter ? $this->counter : $this->counter->get($counter);
    }

    /**
     * Get the messages stack
     *
     * @return MessageStackInterface
     */
    public function getMessages(): MessageStackInterface
    {
        return $this->messages;
    }
    
    /**
     * Get the debugger logger
     * 
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Collect the debugger dump
     *
     * @param boolean $log
     * @return string
     */
    public function dump(bool $log = false): string
    {
        $dump  = serialize(
            [
                'id' => $this->id,
                'timer' => unserialize($this->timer->dump()),
                'counter' => unserialize($this->counter->dump()),
                'logs' => unserialize($this->logger->dump()),
                'messages' => unserialize($this->messages->dump()),
            ]
        );

        if (false !== $log) {
            $this->logger->debug($dump);
        }
        
        return $dump;
    }

    /**
     * Reset the debugger
     * 
     * @return void
     */
    public function reset(): void
    {
        $this->messages->reset();
        $this->counter->reset();
        $this->timer->reset();
    }
}