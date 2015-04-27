<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench\Console;

use Laradic\Console\Command;

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
abstract class BaseCommand extends Command
{

    /**
     * The workbench factory instance
     * @var \Laradic\Workbench\Factory
     */
    protected $workbench;

    /**
     * The laradic config array using dot notation
     * @var mixed
     */
    protected $config;

    /**
     * Instanciates the class
     *
     * @param \Laradic\Workbench\Factory $workbench
     */
    public function __construct()
    {
        parent::__construct();
        $this->workbench = app('workbench');
        $this->config    = array_dot(app('config')->get('laradic/workbench::config'));
    }
}
