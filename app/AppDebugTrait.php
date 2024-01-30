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
            DebuggerPoolInterface::class,
            true,
            true
        );
        
        register_shutdown_function(
            function () use ($debuggerPool) {
                echo "<pre>aaaaaaaaaaaaaaaaaaaaaaaaaaa";
                print_r(unserialize($debuggerPool->dump()));
            }
        );
        
        self::createDebugger('app');
        $debugger = self::getDebugger('app');
        $debugger->getTimer()->start('app');
        $debugger->getCounter()->increment('app');
        $debugger->getCounter()->increment('app');
        $debugger->getLogger()->debug('APP DEBUGGER debug');
        $debugger->getMessages()->push('APP MESSAGES debug');
        $debugger->getTimer()->stop('app');
die('deaergsdthsdfghsfg');
    }

    /**
     * Get the debugger instance
     *
     * @return DebuggerInterface|DebuggerPoolInterface
     */
    static public function getDebugger(?string $id = null): DebuggerInterface
    {
        if (null === $id) {
            return self::getInstance()->debuggerPool;
        }

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
}