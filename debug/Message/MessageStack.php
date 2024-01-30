<?php
namespace XTC\Debug\Message;

class MessageStack implements MessageStackInterface
{
    protected ?array $stack = [];

    /**
     * {@inheritDoc}
     */
    public function getAll(): array
    {
        return $this->stack;
    }

    /**
     * {@inheritDoc}
     */
    public function pop(): string
    {
        return array_pop($this->stack);
    }

    /**
     * {@inheritDoc}
     */
    public function push(string $message): void
    {
        array_push($this->stack, $message);
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): void
    {
        $this->stack = [];
    }

    public function dump(): string
    {
        return serialize($this->stack);
    }
}