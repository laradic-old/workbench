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


use Symfony\Component\VarDumper\VarDumper;

class WorkbenchTestCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists all workbench packages with some additional information.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$c = new Config;
        $name = 'asd-asdff/wegr';

        VarDumper::dump($this->workbench->getPackageNamespace($name));
        //$this->info(json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES, 4));


    }
}
