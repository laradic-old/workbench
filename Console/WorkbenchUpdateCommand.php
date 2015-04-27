<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench\Console;

use Laradic\Console\Traits\SlugPackageTrait;
use Laradic\Support\String;
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
class WorkbenchUpdateCommand extends BaseCommand
{
    use SlugPackageTrait;

    protected $name = 'workbench:update';

    protected $description = 'Update composer packages for a workbench package';

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        if( ! $this->validateSlug( $slug = $this->argument('slug') ) )
        {
            return $this->error('Invalid slug');
        }

        if(!$this->workbench->exists($slug))
        {
            return $this->error("Could not update $slug. The directory does not exists.");
        }

        $this->comment('Using the composer.dev.json file to update');
        $this->workbench->useComposerDevFile($slug);
        $this->workbench->callComposer($slug, 'update');
        $this->workbench->useComposerDistFile($slug);

        $this->info('All done sire!');
    }

    public function getArguments()
    {
        return [
            ['slug', InputArgument::REQUIRED, 'The vendor/package slug']
        ];
    }
}
