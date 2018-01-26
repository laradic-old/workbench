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

namespace Laradic\Workbench;

use Laradic\Workbench\Contextual\Composer\ComposerContextInterface;
use Laradic\Workbench\Contextual\Filesystem\FilesystemContextInterface;
use Laradic\Workbench\Contextual\Git\GitContextInterface;
use Laradic\Workbench\Core\Component;

/**
 * This is the class Project.
 *
 * @property \Laradic\Workbench\Contextual\Composer\ComposerContextual $composer
 * @property \Laradic\Workbench\Contextual\Composer\ComposerContextual $composer
 * @property \Laradic\Workbench\Contextual\Composer\ComposerContextual $fs
 * @property \Laradic\Workbench\Contextual\Git\GitContextual           $git
 * @property \Laradic\Workbench\Contextual\Composer\ComposerContextual $composer
 * @property \Laradic\Workbench\Contextual\Composer\ComposerContextual $composer
 *
 */
class Project extends Component implements FilesystemContextInterface, ComposerContextInterface, GitContextInterface
{

    public function getComposer()
    {
        return $this->composer;
    }

    public function getFiles()
    {
        // TODO: Implement getFiles() method.
    }

    public function path($path = null, $canonicalize = false)
    {
        // TODO: Implement path() method.
    }

    public function getPath()
    {
        // TODO: Implement getPath() method.
    }

    public function getGit()
    {
        // TODO: Implement getGit() method.
    }
}
