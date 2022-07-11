<?php

namespace App\Console\Commands;

use App\Http\Services\ProjectService;
use App\Http\Services\WxAuthService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;

class JwtTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jwt:token {appid} {appsecret}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '根据提供的uid生成token';

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
     * @return mixed
     */
    public function handle()
    {
//        $this->chb();
//        exit("处理完毕");

//        $this->chb1();
//        exit("处理完毕");
        $appid = $this->argument('appid');
        $appsecret = $this->argument('appsecret');
        if (empty($appid)) {
            throw new \Exception("请输入appid参数");
        }
        echo (new ProjectService())->jwtEncode(['appid' => $appid,'appsecret' => $appsecret]);
    }


}
