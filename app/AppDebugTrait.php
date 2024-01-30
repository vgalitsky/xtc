<?php
namespace XTC\App;

use XTC\Debug\Debugger;
use XTC\Debug\DebuggerInterface;
use XTC\Debug\DebuggerPoolInterface;

trait AppDebugTrait
{
    protected ?DebuggerPoolInterface $debuggerPool = null;

    /**
     * Init the app debugger
     *
     * @return void
     */
    public function initDebug(): void
    {
        $debuggerPool = $this->debuggerPool = $this->container->get(
            DebuggerPoolInterface::class
        );
        
        self::createDebugger('app');
        $debugger = self::getDebugger('app');
        $debugger->getTimer()->start('app');
        $debugger->getCounter()->increment('app');
        $debugger->getCounter()->increment('app');
        $debugger->getLogger()->debug('APP DEBUGGER debug');
        $debugger->getMessages()->push('APP MESSAGES debug');
        $debugger->getTimer()->stop('app');

        register_shutdown_function(
            function () {
                echo self::getDebugger('app')->dump();
            }
        );
    }

    /**
     * Get the debugger instance
     * @param string $id The debugger identifier
     *
     * @return DebuggerInterface|DebuggerPoolInterface
     */
    static public function getDebugger(string $id ): DebuggerInterface
    {
        return self::getInstance()->debuggerPool->get($id);
    }

    /**
     * Cretae the debugger with id
     *
     * @param string $id
     * 
     * @return DebuggerInterface
     */
    static function createDebugger(string $id): DebuggerInterface
    {
        return self::getInstance()->debuggerPool->create($id);
    }
    /**
     * Get the debugger pool instance
     *
     * @return DebuggerPoolInterface
     */
    static public function getDebuggerPool(): DebuggerPoolInterface
    {
        return self::getInstance()->debuggerPool;
    }
}