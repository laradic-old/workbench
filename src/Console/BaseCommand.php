<?php

namespace Laradic\Workbench\Console;

use Illuminate\Console\Command;
use Laradic\Workbench\Factory;

abstract class BaseCommand extends Command
{
    /**
     * @var \Laradic\Workbench\Factory
     */
    protected $workbench;

    /**
     * Create a new console command instance.
     *
     * @param \Laradic\Workbench\Factory $workbench
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
