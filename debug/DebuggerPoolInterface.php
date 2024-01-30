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
    public function create(string $id): DebuggerInterface;

    /**
     * Get the debugger from pool
     *
     * @param string $id
     * @param boolean $create
     * 
     * @return DebuggerInterface
     */
    public function get(string $id, bool $create = true): DebuggerInterface;

    /**
     * Check if debugger exists
     *
     * @param string $id
     * @return boolean
     */
    public function has(string $id): bool;
}