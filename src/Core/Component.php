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

namespace Laradic\Workbench\Core;

use Laradic\Workbench\Core\Traits;

abstract class Component
{
    use Traits\Bootable,
        Traits\Extendable,
        Traits\WithContainerAccess;

    public function __construct()
    {
        $this->bootIfNotBooted();
    }
}
