<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsService;

class SyncWwtpToSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wwtp:sync-sheets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all WWTP Analisa data to Google Sheets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting synchronization of WWTP Analisa data to Google Sheets...');
        GoogleSheetsService::sync();
        $this->info('Sync trigger finished! Check your Google Sheet or Laravel logs for status.');
    }
}
