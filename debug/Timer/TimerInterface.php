<?php
namespace XTC\Debug\Timer;

interface TimerInterface
{

    /**
     * Start a timer
     *
     * @param [type] $timer
     * @return float
     */
    public function start(string $timer): float;

    /**
     * Stop a timer
     *
     * @param [type] $timer
     * @return float
     */
    public function stop(string $timer): float;

    /**
     * Get a timer stats
     *
     * @param [type] $timer
     * @return array
     */
    public function get(string $timer): array;

    /**
     * Get a timer start
     *
     * @param [type] $timer
     * @return void
     */
    public function getStart(string $timer): float;
    
    /**
     * Get a timer stop
     *
     * @param [type] $timer
     * @return void
     */
    public function getStop(string $timer): float;

    /**
     * Get a duration
     *
     * @param string $timer
     * 
     * @return void
     */
    public function getDuration(string $timer): float;

    /**
     * Indicates if timer exists
     *
     * @param string  $timer
     * @param boolean $createIfNotExists
     * 
     * @return boolean
     */
    public function has(string $timer, bool $createIfNotExists = false): bool;

    /**
     * {@inheritDoc}
     */
    public function reset(?string $id = null): void;

    public function dump();

}