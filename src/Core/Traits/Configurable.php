<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2018. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2018 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
 */


namespace Laradic\Workbench\Core\Traits;

trait Configurable
{
    protected $config = [];

    public function config($key = null, $default = null)
    {
        return $key === null ? $this->config : array_get($this->config, $key, $default);
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the config value
     *
     * @param mixed $config
     *
     * @return Configurable
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }
}
