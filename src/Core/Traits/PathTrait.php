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

use Laradic\Support\Path;

trait PathTrait
{
    protected $path;

    /**
     * path method
     *
     * @param null|string $path
     * @param bool        $canonicalize
     *
     * @return string
     */
    public function path($path = null, $canonicalize = false)
    {
        $path = $path === null ? $this->path : Path::join($this->path, $path);
        return $canonicalize ? Path::canonicalize($path) : $path;
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
     * @return PathTrait
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
