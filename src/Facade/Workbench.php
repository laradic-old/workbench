<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Workbench\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * This is the Workbench.
 *
 * @package        Laradic\Workbench
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class Workbench extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'workbench';
    }

}
