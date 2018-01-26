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


class WorkbenchCommitCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workbench:commit
                            {name : The "vendor/package" name }
                            {--push=origin : The remote to push to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commit changes for the specified workbench package.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        if ( ! $this->workbench->packageExists($name) )
        {
            return $this->error("Could not update [{$name}]. The package does not exists.");
        }

        $message  = $this->ask('Commit message?', 'Commit on ' . time());
        $push     = $this->confirm('Push to remote?', true);
        $commands = [
            'git add -A',
            'git commit -m "' . $message . '"'
        ];
        if ( $push )
        {
            $commands[] = 'git push -u ' . $this->option('push') . ' ' . $this->workbench->getPackageGitBranch($name);
        }
        $this->workbench->packageCommand($name, $commands);
        $this->info('All done sire!');
    }
}
