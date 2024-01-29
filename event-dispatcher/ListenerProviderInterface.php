<?php
namespace XTC\EventDispatcher;

use Psr\EventDispatcher\ListenerProviderInterface as PsrListenerProviderInterface;

interface ListenerProviderInterface extends PsrListenerProviderInterface
{
    /**
     * Attach the listener
     *
     * @param string $type
     * @param callable $listener
     * @return void
     */
    public function attach(string $type, $listener);
}