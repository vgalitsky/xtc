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
     * @param string $id              The id of the service
     * @param mixed  ...$args         Optional service constructor arguments
     * 
     * @return object The service instance
     */
    public function get(string $id, ...$args): object;
    
    
    /**
     * Add service to a container
     *
     * @param string $id     The service ID
     * @param object $service The service instance
     * 
     * @return void
     * 
     * @throws ServiceAlreadyRegisteredException
     */
    function register(string $id, object $service);

    /**
     * Cretate a service instance
     *
     * @param string $id_or_class The service identifier or classname
     * @param mixed  ...$args     The service constructor arguments
     * 
     * @return object The created service instance
     * 
     * @throws UnableToCreateServiceException
     * @throws ServiceAlreadyRegisteredException

     */
    public function create(string $id_or_class, ...$args): object;
}