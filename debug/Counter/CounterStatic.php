<?php
namespace XTC\Debug\Counter;

class CounterStatic //implements CounterInterface
{
    /**
     * The counters container
     *
     * @var array
     */
    protected static array $counters = [];

    /**
     * {@inheritDoc}
     */
    public static function increment(string $name): void
    {
        if (!isset(static::$counters[$name])) {
            static::$counters[$name] = 0;
        }

        static::$counters[$name]++;
    }

    /**
     * {@inheritDoc}
     */
    public static function decrement(string $name): void
    {
        if (!isset(static::$counters[$name])) {
            static::$counters[$name] = 1;
        }

        static::$counters[$name]--;
    }

    /**
     * {@inheritDoc}
     */
    public static function get(string $name): int
    {
        return static::$counters[$name];
    }
}