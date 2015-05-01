<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench\Providers;

use Laradic\Console\AggregateConsoleProvider;

/**
 * This is the ConsoleServiceProvider class.
 *
 * @package        Laradic\Workbench
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class ConsoleServiceProvider extends AggregateConsoleProvider
{
    protected $namespace = 'Laradic\Workbench\Console';
    protected $commands = [
        'WorkbenchMake' => 'laradic.workbench.make',
        'WorkbenchUpdate' => 'laradic.workbench.update',
        'WorkbenchList' => 'laradic.workbench.list',
        'WorkbenchCommit' => 'laradic.workbench.commit',
        'WorkbenchTest' => 'laradic.workbench.test',
        'WorkbenchDeploy' => 'laradic.workbench.deploy',
    ];
}
