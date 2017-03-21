<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;

/**
 * {@inheritdoc}
 */
class WorkbenchServiceProvider extends ServiceProvider
{

    protected $configFile = __DIR__ . '/../resources/config/laradic.workbench.php';

    #protected $stubsDir = __DIR__ . '/../resources/stubs';

    protected $commands = [
        \Laradic\Workbench\Console\WorkbenchListCommand::class,
        \Laradic\Workbench\Console\WorkbenchMakeCommand::class,
       # \Laradic\Workbench\Console\WorkbenchTestCommand::class,
        \Laradic\Workbench\Console\WorkbenchCommitCommand::class,
        \Laradic\Workbench\Console\WorkbenchBumpCommand::class,
    ];

    protected $defer = true;

    public function provides()
    {
        return [ 'workbench' ];
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->publishes([ $this->configFile => config_path('laradic.workbench.php') ], 'config');
    }

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
        $resolver->register('stub', function () use ($app)
        {
            $compiler = $app->make('blade.compiler');
            return new CompilerEngine($compiler);
        });
        $view->addExtension('stub', 'stub');

    }
}
