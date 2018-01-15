<?php

namespace App\Console\Commands;

use App\Model\Users;
use App\User;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Console\Command;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;
use QL\QueryList;
use Overtrue\EasySms\EasySms;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class remindGrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grade:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    protected $queryList;
    protected const URL = 'http://www.jwc.ldu.edu.cn/cj/';
    protected const URL_POST = 'http://www.jwc.ldu.edu.cn/cj/chaxun.asp';
    public function __construct(QueryList $queryList)
    {
        parent::__construct();
        $this->queryList = $queryList;
    }


    public function handle()
    {
//        $users = new Users();
//        $users = $users->all();
//
//        foreach ($users as $user) {
//            $db_grade = json_decode($user->grade, true);
//            $remote_grade = $this->getGrade($user->student_id, $user->password, $user->name);
//            $diff_arr = array_diff_assoc($db_grade, $remote_grade);
//
//            if (!empty($diff_arr)) {
//                $str = '';
//                foreach ($diff_arr as $key => $value) {
//                    $str .= $key.':'.$value.',';
//                }
//                $update = substr($str,0,strlen($str)-1);
//
//                $this->sendSMS($user->phone, $user->name, $update);
//            }
//        }
        $users = new Users();
        $user = $users->where('student_id', '20152203085')->first();
        $user->name = $user->name.'1';
        $user->save();

    }
    private function getGrade($student_id, $password, $name)
    {
        $ql = QueryList::getInstance();
        //手动设置cookie
        $jar = new CookieJar();

        $ql->get(self::URL,[],[
            'cookies' => $jar
        ]);
        $post_field = 'oper=login&xh='.$student_id.'&xm='.$name.'&mm='.$password.'&button=%E6%9F%A5++%E8%AF%A2';
        $ql->post(self::URL_POST,$post_field,[
            'cookies' => $jar
        ]);

        $str = $ql->getHtml();
        $names = [];
        $scores = [];
        preg_match_all('/<br>课程名：(.*?)<br>/', $str, $names);
        preg_match_all('/<br>总成绩:(.*?)<hr>/', $str, $scores);

        $grades = [];
        foreach ($names[1] as $key => $value)
        {
            $grades[$value] = $scores[1][$key];
        }
        return $grades;
    }

    private function sendSMS(string $phone, string $name, string $update)
    {
        if (strlen($update)>=20)
        {
            $update = '数据过长，请在微信公众号内查看';
        }
        $config = [
            'timeout' => 5.0,
            'default' => [
                'strategy' =>  \Overtrue\EasySms\Strategies\OrderStrategy::class,
                'gateways' => [
                    'aliyun',
                ],
            ],
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'yunpian' => [
                    'api_key' => '824f0ff2f71cab52936axxxxxxxxxx',
                ],
                'aliyun' => [
                    'access_key_id' => env('ALIYUN_ACCESS_KEY'),
                    'access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET'),
                    'sign_name' => 'may2成绩推送',
                ],
            ],
        ];
        $easySms = new EasySms($config);
        try{
            $easySms->send($phone, [
                'content'  => 'content',
                'template' => 'SMS_121907064',
                'data' => [
                    'name' => $name,
                    'update' => $update,
                    'list' => '请到微信公众号内查看'
                ],
            ], ['aliyun']);
        }catch (NoGatewayAvailableException $e)
        {
            $log = new Logger('NoGatewayAvailableException');
            $log->pushHandler(new StreamHandler('storage/logs/error.log', Logger::ERROR));

            $log->error('网关不可用');
        }
    }

}
