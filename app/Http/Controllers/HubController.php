<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HubController extends Controller
{
    public function index()
    {
        return view('hub');
    }
}
