<?php
namespace Laradic\Workbench;

use Laradic\ServiceProvider\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{

    protected $bindings = [
        'laradic.workbench.composer-file'      => ComposerFile::class,
        'laradic.workbench.package.vendor'     => VendorPackage::class,
        'laradic.workbench.package.workbench'  => WorkbenchPackage::class,
        'laradic.workbench.project'            => Project::class,
        'laradic.workbench.package.collection' => PackageCollection::class,
    ];

    protected $shared = [
        'laradic.workbench' => Workbench::class,
    ];
}