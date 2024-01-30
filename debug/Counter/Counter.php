<?php
namespace XTC\Debug\Counter;

class Counter implements CounterInterface
{
    /**
     * The counters container
     *
     * @var array
     */
    protected array $counters = [];

    /**
     * {@inheritDoc}
     */
    public function increment(string $name): void
    {
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = 0;
        }

        $this->counters[$name]++;
    }

    /**
     * {@inheritDoc}
     */
    public function decrement(string $name): void
    {
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = 1;
        }

        $this->counters[$name]--;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name): int
    {
        return $this->counters[$name];
    }
    /**
     * {@inheritDoc}
     */
    public function reset(?string $name = null): void
    {
        if (null === $name ) {
            $this->counters = [];
        } else {
            $this->counters[$name] = 0;
        }
    }

    public function dump(): string
    {
        return serialize($this->counters);
    }
}