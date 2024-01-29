<?php
namespace XTC\Debug\Counter;

class CounterStatic implements CounterInterface
{
    /**
     * The counters container
     *
     * @var array
     */
    protected array $counters = [];

    /**
     * Increment a counter
     *
     * @param string $name Counter name
     * 
     * @return void
     */
    public function increment(string $name): void
    {
        if (!isset(static::$counters[$name])) {
            $this->counters[$name] = 0;
        }

        $this->counters[$name]++;
    }

    /**
     * Decrement a counter
     *
     * @param string $name The counter name
     * 
     * @return void
     */
    public function decrement(string $name): void
    {
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = 1;
        }

        $this->counters[$name]--;
    }

    /**
     * Get the counter value
     *
     * @param string $name The counter name
     * 
     * @return integer
     */
    public function get(string $name): int
    {
        return $this->counters[$name];
    }
}