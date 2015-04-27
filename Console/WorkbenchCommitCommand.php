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
use Symfony\Component\Console\Input\InputOption;

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
class WorkbenchCommitCommand extends BaseCommand
{
    use SlugPackageTrait;

    protected $name = 'workbench:commit';

    protected $description = 'Add all, commit and push the package using git';

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

        $cwd = getcwd();
        chdir($this->workbench->getPackageDir($slug));

        $message = $this->ask('message');

        $this->workbench->useComposerDistFile($slug);
        passthru('git add -A');
        passthru('git commit -m "' . $message . '"');
        passthru('git push -u ' . $this->option('remote') . ' ' . $this->option('branch'));

        chdir($cwd);
        $this->info('All done sire!');
    }

    public function getArguments()
    {
        return [
            ['slug', InputArgument::REQUIRED, 'The vendor/package slug']
        ];
    }

    public function getOptions()
    {
        return [
            ['remote', 'r', InputOption::VALUE_OPTIONAL, 'The remote to push to', 'origin'],
            ['branch', 'b', InputOption::VALUE_OPTIONAL, 'The branch to push to', 'master']
        ];
    }
}
