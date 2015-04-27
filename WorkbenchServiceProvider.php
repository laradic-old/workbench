<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench;

use Illuminate\Foundation\Application;
use Illuminate\View\Engines\CompilerEngine;
use Laradic\Config\Traits\ConfigProviderTrait;
use Laradic\Support\ServiceProvider;

/**
 * {@inheritdoc}
 */
class WorkbenchServiceProvider extends ServiceProvider
{
    use ConfigProviderTrait;

    protected $providers = [
        'Laradic\Workbench\Providers\ConsoleServiceProvider'
    ];

    public function provides()
    {
        return ['workbench'];
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app = parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        /** @var \Illuminate\Foundation\Application $app */
        $app    = parent::register();
        $config = $this->addConfigComponent('laradic/workbench', 'laradic/workbench', __DIR__ . '/resources/config');


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

        # Bind the workbench factory
        $app->singleton('workbench', function (Application $app) use ($config)
        {
            $factory = new Factory($app->make('files'), $app->make('view'), $config);
            return $factory;
        });
    }
}
