<?php
namespace Xtc\App;

interface BootstrapInterface
{
    /**
     * Init the boostrap
     *
     * @return void
     */
    function init();

    /**
     * Get the singleton instance
     *
     * @return BootstrapInterface
     */
    static function getInstance() : BootstrapInterface;

}