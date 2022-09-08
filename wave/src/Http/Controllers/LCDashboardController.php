<?php

namespace Wave\Http\Controllers; 
use Illuminate\Http\Request;
use Wave\Helpers;

class LCDashboardController extends \App\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    { 
        
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        
        $pageName = getPageName(); 
        return view('theme::dashboard.index',['pageName' => $pageName]);
    }
}
