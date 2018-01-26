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

namespace Laradic\Workbench\Old\Console;

use Illuminate\Console\Command;
use Laradic\Workbench\Old\Factory;

abstract class BaseCommand extends Command
{
    /**
     * @var \Laradic\Workbench\Old\Factory
     */
    protected $workbench;

    /**
     * Create a new console command instance.
     *
     * @param \Laradic\Workbench\Old\Factory $workbench
     */
    public function __construct(Factory $workbench)
    {
        parent::__construct();
        $this->workbench = $workbench;
    }

    /**
     * select
     *
     * @param       $question
     * @param array $choices
     * @param null  $default
     * @param null  $attempts
     * @param null  $multiple
     * @return int|string
     */
    public function select($question, array $choices, $default = null, $attempts = null, $multiple = null)
    {

        $choice = $this->choice($question, $choices, $default, $attempts, $multiple);
        foreach ( $choices as $k => $v )
        {
            if ( $choice === $v )
            {
                return $k;
            }
        }
    }


}
