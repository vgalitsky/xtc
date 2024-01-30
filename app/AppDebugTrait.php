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
        $debugger->getLogger()->debug('APP Logger test');
        $debugger->getMessages()->push('APP Debug messages stack test');
        //$debugger->getTimer()->stop('app');

        register_shutdown_function(
            function () {
                if (self::getConfig('app.debug.enabled')) {
                    self::getDebugger('app')->getLogger()->debug(
                        print_r(unserialize(self::getDebugger('app')->dump()), true)
                    );
                }
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
        return self::getInstance()->debuggerPool->getDebugger($id);
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
        return self::getInstance()->debuggerPool->createDebugger($id);
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