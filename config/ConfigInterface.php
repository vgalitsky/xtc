<?php
namespace XTC\Config;

interface ConfigInterface
{
    /**
     * Get the config value by path
     *
     * @param string $path The path e.g. "key.subkey.subsubkey" ...
     * 
     * @return mixed|array|object
     */
    function get(string $path);

    /**
     * Get the all config data
     *
     * @return void
     */
    function all();
}