<?php
namespace XTC\Config;

use InvalidArgumentException;

class Config implements ConfigInterface
{
    /**
     * The path separatog. e.g. "key1.subkey.subsubkey"
     */
    const PATH_SEPARATOR = '.';

    /**
     * Storage
     *
     * @var array
     */
    protected array $config = [];

    /**
     * The constructor
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $path 
     * @param bool   $subNodeAsInstance 
     * 
     * @return mixed
     */
    public function get(string $path, bool $subNodeAsInstance = false)
    {
        $node = $this->pathGet($path, false);
        if (is_array($node) && $subNodeAsInstance) {
            return new static($node);
        }
        return $node;
    }

    /**
     * Get all as array
     *
     * @return array
     */
    public function all(): array
    {
        return $this->get('');
    }

    /**
     * Get the value at the specified path.
     *
     * @param string $path 
     *
     * @return mixed
     * @throws InvalidPathException
     */
    public function pathGet(string $path, bool $throw = false)
    {
        return array_reduce(// Lookup by the path
            $this->splitPath($path), 
            function ($reference, $key) use ($path, $throw) {
                if (!is_array($reference) || !key_exists($key, $reference)) {
                    if (false !== $throw) {
                        throw new InvalidArgumentException(sprintf('Given path "%s" not found', $path));
                    }
                    return null;
                }
                return $reference[$key];
            },
            $this->config
        );
    }
    
    /**
     * Split the path string by a separator. Default is @see const PATH_DEFAULT_SEPARATOR
     * Separator will be ignored inside double quotes.
     * e.g. `"11.2".3.5."another.key"` equals to an array access like $array["11.2"]["3"]["5"]["another.key"]
     *
     * @param string $path the Path string
     * 
     * @return array
     */
    protected function splitPath(string $path): array
    {
        return
            array_filter( // Remove empty items
                array_map( // Trim double quotes
                    fn($item) => trim($item, '"'),
                    preg_split($this->getSplitRegexp(), $path)
                )
            );
    }

    /**
     * Get the regular expression pattern for splitting the path.
     *
     * @return string
     */
    protected function getSplitRegexp(): string
    {
        return sprintf(
            '/%s(?=(?:[^"]*"[^"]*")*(?![^"]*"))/',
            preg_quote(static::PATH_SEPARATOR)
        );
    }
}