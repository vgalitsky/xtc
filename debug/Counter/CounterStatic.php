<?php
namespace XTC\Debug\Counter;

class CounterStatic implements CounterInterface
{
    /**
     * The counters container
     *
     * @var array
     */
    protected static array $counters = [];

    /**
     * Increment a counter
     *
     * @param string $name Counter name
     * 
     * @return void
     */
    public static function increment(string $name): void
    {
        if (!isset(static::$counters[$name])) {
            static::$counters[$name] = 0;
        }

        static::$counters[$name]++;
    }

    /**
     * Decrement a counter
     *
     * @param string $name The counter name
     * 
     * @return void
     */
    public static function decrement(string $name): void
    {
        if (!isset(static::$counters[$name])) {
            static::$counters[$name] = 1;
        }

        static::$counters[$name]--;
    }

    /**
     * Get the counter value
     *
     * @param string $name The counter name
     * 
     * @return integer
     */
    public static function get(string $name): int
    {
        return static::$counters[$name];
    }
}