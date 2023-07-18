<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PushAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdn:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'push asset';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Push assets to CDN
        $this->info('Pushing assets to CDN...');
        // Replace the "YOUR_CDN_PUSH_COMMAND" with the actual command to push assets to your CDN.
        exec('php artisan cdn:push');

        // Remove local assets
        $this->info('Removing local assets...');
        // Replace the "YOUR_ASSET_REMOVAL_COMMAND" with the actual command to remove assets from your local storage.
        exec('rm -rf public/assets');

        $this->info('CDN push and asset removal completed successfully.');
    }
}
