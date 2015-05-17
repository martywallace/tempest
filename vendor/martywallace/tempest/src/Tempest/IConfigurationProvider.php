<?php

namespace Tempest;


/**
 * Describes an object that is able to provide configuration data associated with it.
 *
 * @package Tempest
 * @author Marty Wallace
 */
interface IConfigurationProvider
{

    /**
     * @param string $prop The configuration data to get.
     * @param mixed $fallback A fallback value to use if the specified property does not exist.
     * @return mixed
     */
    public function config($prop, $fallback = null);

}