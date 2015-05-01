<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench\Console;

use Laradic\Console\Traits\SlugPackageTrait;
use Laradic\Support\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Finder\Finder;

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
class WorkbenchTestCommand extends BaseCommand
{

    use SlugPackageTrait;

    protected $name = 'workbench:test';

    protected $description = 'Test';

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        #$fs = new Filesystem();
    }


}
