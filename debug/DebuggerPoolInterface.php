<?php
namespace XTC\Debug;


interface DebuggerPoolInterface
{
    /**
     * Create new debugger and add to pool
     *
     * @param string $id
     * 
     * @return DebuggerInterface
     */
    public function createDebugger(string $id): DebuggerInterface;

    /**
     * Get the debugger from pool
     *
     * @param string $id
     * @param boolean $create
     * 
     * @return DebuggerInterface
     */
    public function getDebugger(string $id, bool $create = true): DebuggerInterface;

    /**
     * Check if debugger exists
     *
     * @param string $id
     * @return boolean
     */
    public function hasDebugger(string $id): bool;
}