<?php
namespace Laradic\Workbench;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Laradic\Filesystem\Filesystem;
use Laradic\Support\Arr;

class ComposerFile implements Arrayable, \ArrayAccess
{
    protected $root = false;

    protected $data;

    protected $path;

    protected $fs;

    /**
     * ComposerFile constructor.
     *
     * @param $path
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public static function make($data)
    {
        $me = app(static::class);
        if ( is_string($data) ) {
            return $me->setPath($data);
        }

        return $me;
    }


    public function isRoot()
    {
        return $this->root;
    }

    public function toCollection()
    {
        return Collection::make($this->data);
    }

    public function load()
    {
        if ( false === $this->fs->exists($this->getPath()) ) {
            throw new FileNotFoundException("Could not load composer file from [{$this->getPath()}]");
        }

        $a = [
            'help' => 'Help\\HelpClass',
        ];
    }

    public function save()
    {
        return $this->saveAs($this->getPath());
    }

    public function saveAs($path, $force = true)
    {
        if ( false === $this->fs->exists($path) || $force ) {
            $this->fs->put($path, $this->data);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path value
     *
     * @param mixed $path
     *
     * @return ComposerFile
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }


    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->data, $key);
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string $key
     * @param  mixed        $value
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        if ( is_array($key) ) {
            foreach ( $key as $innerKey => $innerValue ) {
                Arr::set($this->data, $innerKey, $innerValue);
            }
        } else {
            Arr::set($this->data, $key, $value);
        }
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}