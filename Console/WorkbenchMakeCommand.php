<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench\Console;

use Laradic\Console\Traits\SlugPackageTrait;
use Symfony\Component\Console\Input\InputArgument;

/**
 * This is the WorkbenchMakeCommand class.
 *
 * @package        Laradic\Workbench
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic1
 */
class WorkbenchMakeCommand extends BaseCommand
{
    use SlugPackageTrait;

    protected $name = 'workbench:make';

    protected $description = 'Create a new workbench package';

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        if ( ! $this->validateSlug($slug = $this->argument('slug')) )
        {
            return $this->error('Invalid slug');
        }

        if ( $this->workbench->exists($slug) )
        {
            return $this->error("Could not create $slug. The destination directory already exists.");
        }

        $this->workbench->create($slug);
        $this->info('Created the workbench package structure');

        if($gitInit = $this->ask('Do you want to use radic git:init to initialize git?'))
        {
            $this->workbench->callRadicGitInit($slug);
        }

        $this->workbench->callComposer($slug, 'install');
        $this->comment('Start using this package by adding the service provider to config/app.php providers');
        $this->info('All done sire!');
    }

    public function getArguments()
    {
        return [
            [ 'slug', InputArgument::REQUIRED, 'The vendor/package slug' ]
        ];
    }
}
