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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;

use vierbergenlars\SemVer\version;
use vierbergenlars\SemVer\expression;
use vierbergenlars\SemVer\SemVerException;

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
class WorkbenchDeployCommand extends BaseCommand
{

    use SlugPackageTrait;

    protected $name = 'workbench:deploy';

    protected $description = 'Deploy a workbench package. Commit it, tag it (version bump) and push it.';

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        if ( ! $this->validateSlug($slug = $this->argument('slug')) )
        {
            return $this->error('Invalid slug');
        }

        if ( ! $this->workbench->exists($slug) )
        {
            return $this->error("Could not update $slug. The directory does not exists.");
        }

        $packageDir = $this->workbench->getPackageDir($slug);

        $cwd = getcwd();
        chdir($packageDir);

        exec('git symbolic-ref -q HEAD', $ref);
        $branch = last(explode('/', head($ref)));

        #$this->dump($version->valid());

        $version = $this->getVersion();
        $message = $this->ask('Commit (not tag) message before tagging?', 'Commiting before tagging ' . $version->valid());
        $tagMessage = $this->ask('Tag message?', 'Tagging ' . $version->valid());

        $this->workbench->useComposerDistFile($slug);
        passthru('git add -A');
        passthru('git commit -m "' . $message . '"');
        passthru('git push -u ' . $this->option('remote') . ' ' . $branch);


        passthru('git tag -a ' . $version->valid() . ' -m "' . $tagMessage . '"');
        passthru('git push -u ' . $this->option('remote') . ' ' . $version->valid());

        $this->info('All done sire');
        chdir($cwd);
    }

    /**
     * getVersion
     *
     * @return version
     */
    protected function getVersion()
    {

        exec('git describe --abbrev=0 --tags', $lastTag);
        if(empty($lastTag))
        {
            $version = $this->ask('Initial deployment version', '1.0.0');
        }
        else
        {
            $version = head($lastTag);
        }
        try
        {
            $semver = new version($version);
            if(!empty($lastTag))
            {
                $bumpType = $this->select('Type of version bump?', [
                    'patch' => 'Patch to ' . $semver->inc('patch')->valid(),
                    'minor' => 'Minor to ' . $semver->inc('minor')->valid(),
                    'major' => 'Major to '  . $semver->inc('major')->valid(),
                ], 'patch');
                $semver = new version($version);
                $semver->inc($bumpType);
            }
        }
        catch(\RuntimeException $e)
        {
            $this->error($e->getMessage());
            exit;
        }
        return $semver;
    }

    public function getArguments()
    {
        return [
            [ 'slug', InputArgument::REQUIRED, 'The vendor/package slug' ]
        ];
    }

    public function getOptions()
    {
        return [
            ['remote', 'r', InputOption::VALUE_OPTIONAL, 'The remote to push to', 'origin']
        ];
    }
}
