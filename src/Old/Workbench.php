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

namespace Laradic\Workbench\Old;

use Laradic\Filesystem\Filesystem;

class Workbench
{
    /** @var Filesystem */
    protected $fs;

    /**
     * getProject method
     *
     * @return Project
     */
    public function getProject()
    {
    }

    /**
     * getWorkbenchPackages method
     *
     * @return PackageCollection|WorkbenchPackage[]
     */
    public function getWorkbenchPackages()
    {
        return [];
    }

    public function getPath()
    {
        $path = config('laradic.workbench.dir', 'workbench');
        $path = path_is_relative($path) ? base_path($path) : $path;
        return $path;
    }

    public function getRelativePath()
    {
        return str_remove_left($this->getPath(), base_path());
    }
}
