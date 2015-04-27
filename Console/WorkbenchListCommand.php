<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench\Console;

use Laradic\Support\String;
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
class WorkbenchListCommand extends BaseCommand
{

    protected $name = 'workbench:list';

    protected $description = 'List all workbench packages';

    /**
     * {@inheritdoc}
     */
    public function fire()
    {
        $packages = $this->workbench->getPackages();
        #$this->dump($packages);
        $this->arrayTable($packages, ['Slug', 'Path']);
    }
}
