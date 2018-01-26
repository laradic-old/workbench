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

/**
 * Dot Array Access Trait
 *
 * @author    Laradic Dev Team
 * @copyright Copyright (c) 2015, Laradic
 * @license   https://tldrlegal.com/license/mit-license MIT License
 * @package   Laradic\Support
 */
trait DotArrayTrait
{
    /**
     * Get array accessor.
     *
     * @return mixed
     */
    abstract protected function getArrayAccessor();

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_has($this{$this->getArrayAccessor()}, $offset);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return array_get($this->{$this->getArrayAccessor()}, $offset);
    }

    /**
     * Set the item at a given offset.
     *
     * @param        $offset
     * @param  mixed $value
     *
     * @return $this
     * @internal param mixed $key
     */
    public function offsetSet($offset, $value = null)
    {
        if (is_array($offset)) {
            foreach ($offset as $innerKey => $innerValue) {
                array_set($this->{$this->getArrayAccessor()}, $innerKey, $innerValue);
            }
        } else {
            array_set($this->{$this->getArrayAccessor()}, $offset, $value);
        }

        return $this;
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string $key
     *
     * @return $this
     */
    public function offsetUnset($key)
    {
        array_set($this->{$this->getArrayAccessor()}, $key, null);

        return $this;
    }

    /**
     * getIterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->{$this->getArrayAccessor()});
    }
}
