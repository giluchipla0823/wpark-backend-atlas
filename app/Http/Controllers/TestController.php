<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compound;
use App\Models\User;

class TestController extends Controller
{
    //
    public function test(){
        $response = User::find(1)->compounds;
        dd($response);
    }
}
