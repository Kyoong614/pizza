<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

class UserController extends Controller
{
    //direct admin home page
    public function index(){
        return view('user.home');
    }
}
