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

use Laradic\ServiceProvider\ServiceProvider;
use Laradic\Workbench\Contextual\Composer\ComposerContextual;

class WorkbenchServiceProvider extends ServiceProvider
{
    protected $configFiles = [ 'laradic.workbench' ];

    protected $findCommands = [ 'Console' ];

    protected $shared = [ 'workbench' => Workbench::class ];

    public function register()
    {
        $app = parent::register();

        Project::extend('composer', ComposerContextual::class);

        Project::extend('workbench', Workbench::class);
    }


}
