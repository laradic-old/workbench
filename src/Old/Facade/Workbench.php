<?php
/**
 * Part of the Laradic PHP Packages.
 *
 * Copyright (c) 2018. Robin Radic.
 *
 * The license can be found in the package and online at https://laradic.mit-license.org.
 *
 * @copyright Copyright 2018 (c) Robin Radic
 * @license https://laradic.mit-license.org The MIT License
 */

namespace Laradic\Workbench\Old\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * This is the Workbench.
 *
 * @package        Laradic\Workbench\Old
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
