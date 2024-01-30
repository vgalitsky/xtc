<?php
namespace XTC\Debug\Message;


interface MessageStackInterface
{
    /**
     * get the messages pool
     *
     * @return array
     */
    public function getAll(): array;

    public function pop(): string;
    public function push(string $message): void;
    public function reset(): void;

    public function dump(): string;
}