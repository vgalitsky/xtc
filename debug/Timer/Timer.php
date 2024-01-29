<?php
namespace XTC\Debug\Timer;

class Timer implements TimerInterface
{

    /**
     * @var array The timers container
     */
    private array $timers = [];

    /**
     * Start a timer
     *
     * @param [type] $timer
     * @return float
     */
    public function start(string $timer): float
    {
        $this->has($timer, true);

        return $this->timers[$timer]['start'] = microtime(true);
    }

    /**
     * Stop a timer
     *
     * @param string $timer
     * 
     * @return float
     */
    public function stop(string $timer): float
    {
        if (!$this->has($timer)) {
            return 0;
        }
        return $this->timers[$timer]['stop'] = microtime(true);
    }

    /**
     * Get a timer stats
     *
     * @param string $timer
     * 
     * @return array
     */
    public function get(string $timer): array
    {
        if ($this->has($timer)) {
            return [
                'start' => $this->getStart($timer),
                'stop' => $this->getStop($timer),
            ];
        }
    }

    /**
     * Get a timer start
     *
     * @param string $timer
     * 
     * @return void
     */
    public function getStart(string $timer): float
    {
        if ($this->has($timer) ) {
            return $this->timers[$timer]['start'];
        }
        return 0;
    }
    
    /**
     * Get a timer stop
     *
     * @param string $timer
     * 
     * @return void
     */
    public function getStop(string $timer): float
    {
        if ($this->has($timer) ) {
            return $this->timers[$timer]['stop'];
        }
        return 0;
    }

    /**
     * Indicates if timer exists
     *
     * @param string  $timer
     * @param boolean $createIfNotExists
     * 
     * @return boolean
     */
    public function has(string $timer, bool $createIfNotExists = false): bool
    {
        if (!array_key_exists($timer, $this->timers)) {
            if ($createIfNotExists) {
                $this->timers[$timer] = [];
            }
            return false;
        }
        return true;
    }
}