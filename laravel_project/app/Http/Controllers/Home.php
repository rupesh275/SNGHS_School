<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Master;
use App\Mail\TestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Mail;

class Home extends Controller
{
    protected $request;
       public function __construct(Request $request)
    {
        $this->request = $request;
        
    }

    public function index()
    {
        session(['menu' => 'Dashboard']);
        // print_r(session()->all());die;
        $data = [
            'title' =>  'Dashboard',
            'pageTitle' => 'Dashboard', 'li_1' => 'Admin', 'li_2' => 'Dashboard'
        ];
        return view('index', $data);
    }

}