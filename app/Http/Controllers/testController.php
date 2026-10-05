<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\User;
use Illuminate\Http\Request;


class testController extends Controller
{
    public function test(){
        $nama="bro";
        $alats= Alat::all();
        $users= User::all();
        return view('test.test', compact('nama', 'alats', 'users'));
    }
    
}
