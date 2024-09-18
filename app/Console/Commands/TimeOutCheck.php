<?php

namespace App\Console\Commands;

use App\Http\Controllers\LineController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TimeOutCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wash:timeout_check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check wash timeout and update status to timeout';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //confirm_at 為15分鐘以前的資料

        $washes = \App\Models\Wash::where('confirm_at', '<', date('Y-m-d H:i:s', strtotime('-15 minute')))
            ->whereIn('status', ['get_pay_link', 'confirmed'])
            ->get();

        $line = new LineController();
        $keyword = '客戶逾時未回覆';
        foreach ($washes as $wash) {
            $input = (object) [
                'keyword' => $keyword,
                'value' => $wash->id
            ];
            $line->actionTrigger($input, 'customer');
        }

        return 0;
    }
}
