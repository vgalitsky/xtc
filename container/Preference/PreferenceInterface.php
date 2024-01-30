<?php
namespace XTC\Container\Preference;

interface PreferenceInterface
{
    function info(?string $key = null);
    
    public function uid();
    public function getReference();
    
    public function getClass();


    public function setReferrer(Preference $referrer): void;
    public function getReferrer();
}