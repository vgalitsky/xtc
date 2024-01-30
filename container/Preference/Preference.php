<?php
namespace XTC\Container\Preference;

class Preference implements PreferenceInterface
{
    protected array $info = [];

    /**
     * The constructor 
     *
     * @param array $info Data
     */
    public function __construct(string $uid, array $info)
    {
        $this->info = $info + ["uid"=>$uid];
    }

    /**
     * Get the info
     *
     * @param string|null $key The info data key 
     * 
     * @return array|string|null The whole info array or value or null if not found
     */
    public function info(?string $key = null)
    {
        if (null !== $key) {
            if (array_key_exists($key, $this->info)) {
                return $this->info[$key];
            }
            return null;
        }
        return $this->info();
    }

    /**
     * The preference UID
     *
     * @return string
     */
    public function uid(): string
    {
        return $this->info("uid");
    }

    /**
     * Get the id info
     *
     * @return string|null
     */
    public function getReference()
    {
        return $this->info('reference');
    }
    
    /**
     * Get the class info
     *
     * @return string|null
     */
    public function getClass()
    {
        return $this->info('class');
    }

    /**
     * If service lyfecycle is singleton
     *
     * @return bool
     */
    public function isSingleton(): bool
    {
        return null !== $this->info('singleton') ? $this->info('singleton') : false;
    }

    /**
     * Set the referrer
     *
     * @param Preference $referrer
     * 
     * @return void
     */
    public function setReferrer(Preference $referrer): void
    {
        $this->info['___referrer'] = $referrer;
    }

    /**
     * Get the referrer
     *
     * @return Preference|null
     */
    public function getReferrer()
    {
        return $this->info['___referrer'];
    }
    
    /**
     * Has the referrer
     *
     * @return bool
     */
    public function hasReferrer()
    {
        return 
            array_key_exists('___referrer', $this->info)
            && $this->info['___referrer'] instanceof Preference;
    }
    
    /**
     * Get the arguments info
     *
     * @return array|null
     */
    public function getArguments()
    {
        return $this->info('arguments');
    }
}