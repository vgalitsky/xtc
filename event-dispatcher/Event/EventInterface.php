<?php
namespace XtC\EventDispatcher\Event;


use Psr\EventDispatcher\StoppableEventInterface;

interface EventInterface Extends StoppableEventInterface
{
    public function getConttext();

    public function setStopPropagation(bool $stop = true);
}