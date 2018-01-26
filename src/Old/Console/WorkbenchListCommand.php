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


class WorkbenchListCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:list';

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
        $packages = $this->workbench->getPackages();

        $header = ['Slug', 'Version', 'Branch'];
        $rows = [ ];
        foreach ( $packages as $slug => $info )
        {
            $rows[ ] = [ (string)$slug, (string)$info['version'], (string)$info['branch'] ];
        }
        $this->table($header, $rows);
    }
}
