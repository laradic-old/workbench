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

use Illuminate\Contracts\Container\Container;

trait WithContainerAccess
{
    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Returns the IoC container.
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        if ( ! isset($this->container)) {
            $this->container = \Illuminate\Container\Container::getInstance();
        }
        $this->app = $this->container;
        return $this->container;
    }

    /**
     * Sets the IoC container instance.
     *
     * @param  \Illuminate\Container\Container $container
     *
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
        $this->app       = $this->container;
        return $this;
    }
}
