<?php
declare(strict_types=1);

namespace XTC\Cache;

use Psr\SimpleCache\CacheInterface;

class NullCache implements CacheInterface
{
     /**
      * Undocumented function
      *
      * @param string $key
      * @param mixed $default
      * @return mixed
      */
    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }
        return null;
    }

    
    public function set($key, $value,$ttl = null): bool
    {
        return true;
    }

    
    public function delete($key): bool
    {
        return true;
    }

    
    public function clear(): bool
    {
        return true;
    }

    
    public function getMultiple($keys, $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $item = $this->get($key, $default);
            if (!empty($item)) {
                $result[$key] = $item;
            }
        }
        return $result;
    }

    
    public function setMultiple($values, $ttl = null): bool
    {
        return true;
    }

    
    public function deleteMultiple($keys): bool
    {
        return true;
    }

    
    public function has($key): bool
    {
        return false;
    }
}