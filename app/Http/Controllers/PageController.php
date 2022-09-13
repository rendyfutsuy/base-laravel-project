<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function index()
    {
        return view('app');
    }

    public function auth()
    {
        return view('authentication');
    }
}
