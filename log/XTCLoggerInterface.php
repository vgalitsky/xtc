<?php
namespace XTC\Log;
use Psr\Log\LoggerInterface;

interface XTCLoggerInterface extends LoggerInterface
{
    public function dump(): string;

}