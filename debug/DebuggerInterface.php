<?php
namespace XTC\Debug;

use Psr\Log\LoggerInterface;
use XTC\Debug\Message\MessageStackInterface;
use XTC\Debug\Timer\TimerInterface;

interface DebuggerInterface
{

    public function getTimer(?string $timer = null): TimerInterface;
    
    /**
     * Get the debugger Counters
     * If the $counter is specified than return int (counter result)
     *
     * @param string $counter
     * 
     * @return CounterInterface|int
     */
    public function getCounter(?string $counter = null);

    /**
     * Get the messages stack
     *
     * @return MessageStackInterface
     */
    public function getMessages(): MessageStackInterface;
    
    /**
     * Get the debugger logger
     * 
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * Reset the debugger
     * 
     * @return void
     */
    public function reset(): void;

    public function dump(bool $log = false): string;
}