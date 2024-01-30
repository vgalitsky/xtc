<?php
namespace XTC\Debug;


use XTC\Container\FactoryInterface;

class DebuggerPool implements  DebuggerPoolInterface
{

    /**
     * @var DebuggerInterface[]
     */
    protected ?array $debuggers = [];

    /**
     * @var FactoryInterface
     */
    protected $factory = null;

    /**
     * {@inheritDoc}
     */
    // public function __construct(PsrContainerInterface $container) 
    // {
    //     $this->container = $container;
    // }
    public function __construct(\XTC\Debug\DebuggerFactory $factory) 
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function createDebugger(string $id): DebuggerInterface
    {
        $this->debuggers[$id] = $this->factory->create(DebuggerInterface::class, $id);

        return $this->debuggers[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function getDebugger(string $id, bool $create = true): DebuggerInterface
    {
        if ($this->hasDebugger($id)) {
            return $this->debuggers[$id];
        }

        if ($create) {
            return $this->create($id);
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function hasDEbugger(string $id): bool
    {
        return array_key_exists($id, $this->debuggers);
    }

    public function dump(bool $log = false)
    {
        $dump = [];
        foreach ($this->debuggers as $id => $debugger) {
            $dump[$id] = unserialize($debugger->dump($log));
        }

        return serialize($dump);
    }

    public function shutdown()
    {
        $dump = $this->dump(true);
        echo "<pre>";
        print_r(unserialize($dump));
        die('asdas');
    }
        
}