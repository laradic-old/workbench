<?php

namespace Laradic\Workbench\Console;


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

        $header = ['Slug', 'Version', 'Path'];
        $rows = [ ];
        foreach ( $packages as $slug => $info )
        {
            $rows[ ] = [ (string)$slug, (string)$info['version'], (string)$info['path'] ];
        }
        $this->table($header, $rows);
    }
}
