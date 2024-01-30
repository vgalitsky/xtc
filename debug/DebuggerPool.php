<?php
namespace XTC\Debug;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use XTC\Container\ContainerInterface;

class DebuggerPool implements  DebuggerPoolInterface
{

    /**
     * @var DebuggerInterface[]
     */
    protected ?array $debuggers = [];

    /**
     * @var ContainerInterface|null
     */
    protected ?ContainerInterface $container = null;

    protected $debuggerFactory = null;

    /**
     * {@inheritDoc}
     */
    // public function __construct(PsrContainerInterface $container) 
    // {
    //     $this->container = $container;
    // }
    public function __construct(FactoryInterface $debuggerFactory) 
    {
        $this->debuggerFactory = $debuggerFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $id): DebuggerInterface
    {
        $this->debuggers[$id] = $this->container->create(DebuggerInterface::class, $id);

        return $this->debuggers[$id];
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id, bool $create = true): DebuggerInterface
    {
        if ($this->has($id)) {
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
    public function has(string $id): bool
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
    }
        
}