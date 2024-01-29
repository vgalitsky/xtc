<?php
namespace XTC\Container;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use XTC\Container\Exception\ServiceAlreadyRegisteredException;
use XTC\Container\Exception\UnableToCreateServiceException;



interface ContainerInterface extends PsrContainerInterface
{
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
     * 
     * @return void
     * @throws UnableToCreateServiceException
     * @throws ServiceAlreadyRegisteredException

     */
    function create(string $id_or_class): object;
}