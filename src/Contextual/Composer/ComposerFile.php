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

namespace Laradic\Workbench\Contextual\Composer;

use ArrayAccess;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Laradic\Support\Arr;
use Symfony\Component\Process\ProcessBuilder;

class ComposerFile implements ArrayAccess, Arrayable
{
    protected $raw;

    protected $data;

    protected $saveEnabled = true;

    protected $filePath;

    protected $isLoaded = false;

    public function __construct($path = null, $load = false)
    {
        $this->filePath = $path ? $path : path_join(getcwd(), 'composer.json');
        if ($load) {
            $this->load();
        }
    }

    public function getPath()
    {
        return $this->filePath; // $this->package->path($this->filePath);
    }

    protected function load()
    {
        $this->data     = json_decode($this->raw = file_get($this->filePath), true);
        $this->isLoaded = true;
        return $this;
    }

    protected function save()
    {
        if ( ! $this->saveEnabled) {
            return;
        }
        file_put($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public function reload()
    {
        $this->load();
        return $this;
    }

    public function set($key, $value = null)
    {
        if ( ! $this->isLoaded) {
            $this->load();
        }
        data_set($this->data, $key, $value, true);
        $this->save();
        return $this;
    }

    public function add($key, $value)
    {
        $data   = (array)$this->get($key, []);
        $data[] = $value;
        $this->set($key, $data);
        return $this;
    }

    public function remove($key, $value)
    {
        $data = $this->get($key, []);
        $this->set($key, Arr::removeValue($data, $value));
        $this->save();
        return $this;
    }

    public function forget($key)
    {
        if ( ! $this->isLoaded) {
            $this->load();
        }
        $this->data = Arr::remove($this->data, $key);
        $this->save();
        return $this;
    }

    public function get($key, $default = null)
    {
        if ( ! $this->isLoaded) {
            $this->load();
        }
        return data_get($this->data, $key, $default);
    }

    public function has($key)
    {
        return $this->get($key) !== null;
    }


    ##
    ## Commands
    ##

    public function run($args = [], Closure $cb = null)
    {
        if (is_string($args)) {
            $args = func_get_args();
            foreach ($args as $arg) {
                if ($arg instanceof Closure) {
                    $cb = $arg;
                }
            }
        }

        $builder = ProcessBuilder::create($args);
        $builder->setWorkingDirectory(path_get_directory($this->filePath));
        $builder->setPrefix('composer');
        return $builder->getProcess()->mustRun($cb);
    }

    public function dumpautoload(Closure $cb = null)
    {
        return $this->run('dumpautoload', $cb);
    }

    public function update(Closure $cb = null)
    {
        return $this->run('update', $cb);
    }

    public function install(Closure $cb = null)
    {
        return $this->run('install', $cb);
    }



    ##
    ## Getter/setters
    ##

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @param mixed $raw
     *
     * @return ComposerFile
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * @return boolean
     */
    public function canSave()
    {
        return $this->saveEnabled;
    }

    public function enableSave()
    {
        $this->saveEnabled = true;
        return $this;
    }

    public function disableSave()
    {
        $this->saveEnabled = false;
        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->forget($offset);
    }
}
