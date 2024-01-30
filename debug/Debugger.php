<?php

namespace XTC\Debug;

use Psr\Log\LoggerInterface;

use XTC\App\Bootstrap;
use XTC\Container\FactoryInterface;
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
     * @var FactoryInterface|null
     */
    protected ?FactoryInterface $factory = null;

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
     * @param FactoryInterface $factory
     */
    public function __construct(
        MessageStackInterface $messages, 
        CounterInterface $counter,
        TimerInterface $timer,
        FactoryInterface $loggerFactory,
        string $id
    ) {
        //$this->factory = $factory;
        $this->messages = $messages;
        $this->counter = $counter;
        $this->timer = $timer;
        $this->logger = $loggerFactory->create(
            LoggerInterface::class,
            Bootstrap::getBasePath('/log/xtc/debugger/' . $id . '.log')
        );

        // register_shutdown_function(
        //     function () {}
        // );
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