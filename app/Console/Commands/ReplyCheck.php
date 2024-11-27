<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReplyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wash:reply_check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '時間內未回覆';

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
        $business_times = config('wash.business_times');
        $business_days = config('wash.business_days');
        $business_time['start'] = $business_times[0];
        $business_time['end'] = end($business_times);



        //上班時間內才提醒
        if (!in_array(date('N'), $business_days) || date('H:i') < $business_time['start'] || date('H:i') > $business_time['end']) {
            return 0;
        }


        //在時間內建立，但5分鐘內未回覆 則重新提醒
        //updated_at==created_at 視為未回覆
        $washes = \App\Models\Wash::where('created_at', '<', date('Y-m-d H:i:s', strtotime('-10 minute')))
            // ->where('updated_at', '=', \App\Models\Wash::CREATED_AT)
            ->where('status', 'created')
            ->get();



        $line = new \App\Http\Controllers\LineController();
        $keyword = '預約未回覆通知';
        foreach ($washes as $wash) {
            if ($wash->created_at == $wash->updated_at) {
                //除去 秒數 如果時間剛好是 15分鐘 或是 155+15N分鐘 才執行

                $diff = strtotime(date('Y-m-d H:i')) - strtotime(date('Y-m-d H:i', strtotime($wash->created_at)));
                if ($diff == 5 * 60 || ($diff - 5 * 60) % (10 * 60) == 0) {

                    $input = (object) [
                        'keyword' => $keyword,
                        'value' => $wash->id
                    ];
                    $line->actionTrigger($input, 'customer');
                }
            }
        }
        $washes = \App\Models\Wash::where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-12 minute')))
            // ->where('updated_at', '=', \App\Models\Wash::CREATED_AT)
            ->where('status', 'get_pay_link')
            ->get();

        foreach ($washes as $wash) {
            (new \App\Http\Controllers\WashController())->paidCheck($wash);
        }


        return 0;
    }
}
