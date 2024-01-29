<?php
namespace XTC\EventDispatcher\Event;

class Event implements EventInterface
{
    protected array $context = [];

    protected bool $stopped = false;

    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

    public function getConttext()
    {
        return $this->context;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }

    public function setStopPropagation(bool $stop = true)
    {
        $this->stopped = $stop;
    }
}