<?php
namespace XTC\Debug\Timer;

class Timer implements TimerInterface
{

    /**
     * @var array The timers container
     */
    protected array $timers = [];

    public function __construct()
    {
        register_shutdown_function(
            function () {
                $this->stopAll();
            }
        );
    }

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
        $this->timers[$timer]['stop'] = microtime(true);
        $this->timers[$timer]['duration'] = $this->timers[$timer]['stop'] - $this->timers[$timer]['start'];

        return $this->timers[$timer]['duration'];
    }

    /**
     * Stop all timers
     *
     * @return void
     */
    public function stopAll()
    {
        foreach ($this->timers as $timer => $tmp) {
            $this->stop($timer);
        }
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
                'duration' => $this->getDuration($timer),
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
     * Get a duration
     *
     * @param string $timer
     * 
     * @return void
     */
    public function getDuration(string $timer): float
    {
        if ($this->has($timer) ) {
            return $this->timers[$timer]['duration'];
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

    public function reset(?string $id = null): void
    {
        if( null === $id ) {
            $this->timers = [];
        } else {
            $this->timers[$id] = [];
        }
    }

    public function dump(): string
    {
        return serialize($this->timers);
    }

}