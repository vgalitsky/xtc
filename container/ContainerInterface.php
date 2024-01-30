<?php
namespace XTC\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use XTC\Container\Exception\ServiceAlreadyRegisteredException;
use XTC\Container\Exception\UnableToCreateServiceException;



interface ContainerInterface extends PsrContainerInterface
{

    /**
     * {@inheritDoc}
     *
     * @param string $id
     * @param bool   $create           Whether to create
     * @param bool   $registerCreated  Whether to register
     * @param mixed ...$args           Service constructor arguments
     * 
     * @return object The service instance
     */
    public function get(string $id, bool $create = false, bool $registerCreated = false,  ...$args): object;
    
    
    /**
     * Add service to a container
     *
     * @param string $id     The service ID
     * @param object $service The service instance
     * 
     * @return void
     * @throws ServiceAlreadyRegisteredException
     */
    function register(string $id, object $service);

    /**
     * Cretate a service instance
     *
     * @param string $id_or_class The service identifier or classname
     * @param mixed  ...$args     The service constructor arguments
     * 
     * @return void
     * @throws UnableToCreateServiceException
     * @throws ServiceAlreadyRegisteredException

     */
    public function create(string $id_or_class, ...$args): object;
}