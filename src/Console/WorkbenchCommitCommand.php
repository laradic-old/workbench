<?php

namespace Laradic\Workbench\Console;


use Composer\Config;

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
