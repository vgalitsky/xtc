<?php
namespace XTC\Log;


use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger implements XTCLoggerInterface
{
    protected string $filename = '';

    protected bool $throwable = true;

    //@TODO:VG
    protected array $logs = [];
    protected bool $collectMessages = true;

    public function __construct(string $filename = '')
    {
        $this->filename = $filename ?? 'simple-log.log';
    }

    /**
     * The simple logger
     *
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = [])
    {
        $message = $this->interpolateMessage($message, $context);

        if (false !== $this->collectMessages) {
            $this->logs[] = $message;
        }

        try {

            if (!is_dir(dirname($this->filename))) {
                mkdir(dirname($this->filename), 0777, true);
            }

            $fh = fopen($this->filename, 'a');
            fwrite($fh, '['. date('Y-m-d H:i:s'). '] '. strtoupper($level). ': '. $message. PHP_EOL);
            fclose($fh);

        } catch (\Throwable $e) {
            if (true === $this->throwable) {
                throw $e;
            }
        }
    }

    /**
     * Replace the values from context
     *
     * @param string $message 
     * @param array  $context 
     * 
     * @return string
     */
    protected function interpolateMessage(string $message, array $context = []): string
    {
        foreach ($context as $key => $item) {
            if (is_string($item)) {
                $message = str_replace('{'.$key.'}', $item, $message);
            }
        }
        return $message;
    }

    /**
     * If log can throw an exceptions. e.g. cannot open the file
     *
     * @param bool $throwable True or False
     * 
     * @return void
     */
    public function throwable(bool $throwable = true): void
    {
        $this->throwable = $throwable;
    }
    
    /**
     * Dump messages
     *
     * @return string
     */
    public function dump(): string
    {
        return serialize($this->logs);
    }
}