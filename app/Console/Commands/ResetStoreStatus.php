<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Store;

class ResetStoreStatus extends Command
{
    protected $signature = 'stores:reset';
    protected $description = 'Reset all stores status to unpaid';

    public function handle()
    {
        Store::query()->update(['status' => 'unpaid']);
        $this->info('All store statuses have been reset to unpaid.');
    }
}
