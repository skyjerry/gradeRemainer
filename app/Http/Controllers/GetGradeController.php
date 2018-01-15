<?php

namespace App\Http\Controllers;

use App\Model\Users;
use Illuminate\Http\Request;
use QL\QueryList;
use \GuzzleHttp\Cookie\CookieJar;

class GetGradeController extends Controller
{
    protected $queryList;
    protected $users;

    protected const URL = 'http://www.jwc.ldu.edu.cn/cj/';
    protected const URL_POST = 'http://www.jwc.ldu.edu.cn/cj/chaxun.asp';

    public function __construct(QueryList $queryList, Users $users)
    {
        $this->queryList = $queryList;
        $this->users = $users;
    }

    public function index()
    {

        $student_id = '20152203085';
        $name = '王振';
        $password = '19980428';
        $phone  = '13031639553';

        //dd($grades);
//        $data_array = [
//            'student_id' => $student_id,
//            'password' => $password,
//            'name' => $name,
//            'grade' => json_encode($grades),
//            'phone' => $phone
//        ];
//        $users = new Users();
//        $res = $users->create($data_array);
//        dd($res);

//        $c = $grades;
//        $c['编译原理'] = '99';
//        $arr = array_diff_assoc($grades, $c);
//        dd($arr);
    }
}
