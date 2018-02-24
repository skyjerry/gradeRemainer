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

    public function __construct(QueryList $queryList, Users $users)
    {
        $this->queryList = $queryList;
        $this->users = $users;
    }

    public function index()
    {

    }
}
