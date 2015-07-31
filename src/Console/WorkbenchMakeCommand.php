<?php

namespace Laradic\Workbench\Console;


use Illuminate\Support\Str;

class WorkbenchMakeCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:make {name : The "vendor/package" name }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new workbench package.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        list($vendor, $package) = explode('/', $name);

        $files = [
            'composer.json.stub'              => 'composer.json',
            'gitignore.stub'                  => '.gitignore',
            'phpunit.xml.stub'                => 'phpunit.xml',
            'travis.yml.stub'                 => 'travis.yml',
            'resources/config/config.stub'    => false,
            'src/PackageServiceProvider.stub' => ucfirst($package) . 'ServiceProvider.php',
        ];


        try
        {
            $this->workbench->generate($name, $files);

            if ( $this->confirm("Initialize git for package [{$name}] ?", true) )
            {
                $this->workbench->packageCommand($name, [
                    'git init',
                    'git config --global push.default matching',
                    'git add -A',
                    'git commit -m "Initial commit"'
                ]);
            }
            if ( $this->confirm('Update composer.json config?', true) )
            {
                $this->workbench->updateComposerFile([
                    'replace' => [
                        $name => '*'
                    ],
                    'autoload' => [
                        'psr-4' => [
                            $this->workbench->getPackageNamespace($name) . '\\' => $this->workbench->getPackageDir($name, true) . '/src'
                        ]
                    ],
                    'extra' => [
                        'merge-plugin' => [
                            'include' => [
                                $this->workbench->getPackageDir($name, true) . '/composer.json'
                            ]
                        ]
                    ]
                ]);
                $this->workbench->composerCommand('dumpautoload');
            }

            $this->info("Generated workbench package [{$name}]");
        }
        catch (\Exception $e)
        {
            $this->error($e->getMessage());
        }
    }

}
