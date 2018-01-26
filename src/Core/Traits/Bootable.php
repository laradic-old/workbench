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


trait Bootable
{
    use WithEventAccess;

    protected static $booted = [];

    /**
     * Check if the model needs to be booted and if so, do it.
     *
     * @return void
     */
    protected function bootIfNotBooted()
    {
        $class = get_class($this);

        if ( ! isset(static::$booted[ $class ])) {
            static::$booted[ $class ] = true;

            $this->fireEvent('booting', false);

            static::boot();

            $this->fireEvent('booted', false);
        }
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        static::bootTraits();
    }

    /**
     * Boot all of the bootable traits on the model.
     *
     * @return void
     */
    protected static function bootTraits()
    {
        foreach (class_uses_recursive(get_called_class()) as $trait) {
            if (method_exists(get_called_class(), $method = 'boot' . class_basename($trait))) {
                forward_static_call([ get_called_class(), $method ]);
            }
        }
    }

    /**
     * Clear the list of booted models so they will be re-booted.
     *
     * @return void
     */
    public static function clearBooted()
    {
        static::$booted = [];
    }

    public function __wakeup()
    {
        $this->bootIfNotBooted();
    }

    public static function booting($callback)
    {
        static::registerEvent('booting', $callback);

        return static::class;
    }

    public static function booted($callback)
    {
        static::registerEvent('booted', $callback);

        return static::class;
    }
}
