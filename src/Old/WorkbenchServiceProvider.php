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

use Illuminate\View\Engines\CompilerEngine;
use Laradic\ServiceProvider\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    protected $configFiles = [ 'laradic.workbench' ];

    protected $findCommands = [ 'Console' ];

    protected $shared = [ 'workbench' => Factory::class ]

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configFile, 'laradic.workbench');
        $this->registerStubCompiler();
        $this->app->singleton('workbench', 'Laradic\Workbench\Factory');
        $this->commands($this->commands);
    }

    protected function registerStubCompiler()
    {
        $app = $this->app;

        /** @var \Illuminate\View\Factory $view */
        $view     = $app->make('view');
        $resolver = $app->make('view.engine.resolver');

        #$view->addNamespace('laradicWorkbench', $config[ 'stubs_path' ]);
        $resolver->register('stub', function () use ($app) {
            $compiler = $app->make('blade.compiler');
            return new CompilerEngine($compiler);
        });
        $view->addExtension('stub', 'stub');
    }
}
