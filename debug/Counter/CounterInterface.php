<?php
namespace XTC\Debug\Counter;

interface CounterInterface
{
    /**
     * Increment a counter
     *
     * @param string $name Counter name
     * 
     * @return void
     */
    public function increment(string $name): void;

    /**
     * Decrement a counter
     *
     * @param string $name The counter name
     * 
     * @return void
     */
    public function decrement(string $name): void;

    /**
     * Get the counter value
     *
     * @param string $name The counter name
     * 
     * @return integer
     */
    public function get(string $name): int;
    
    /**
     * Reset the counter
     *
     * @param string $name The counter name
     * 
     * @return integer
     */
    public function reset(?string $name = null): void;

    public function dump(): string;
    
}