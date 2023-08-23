<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportService;

class ImportData extends Command
{
    protected $signature = 'import:master-data';
    protected $description = 'Import master data from URLs into API Webshop database';

    protected $importService;

    public function __construct(ImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    public function handle()
    {
        $importResults = $this->importService->import();

        $this->info('Master data import completed.');
        $this->info('Import results:');
        $this->table(['Data Type', 'Imported Count'], $importResults);
    }
}
