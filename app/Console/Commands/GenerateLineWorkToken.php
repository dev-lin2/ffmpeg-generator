<?php

namespace App\Console\Commands;

use App\Services\LineWorkService;
use Illuminate\Console\Command;

class GenerateLineWorkToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'linework:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Linework Auth Token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lineWorkService = new LineWorkService();
        $lineWorkService->generateAndSaveToken();
    }
}
