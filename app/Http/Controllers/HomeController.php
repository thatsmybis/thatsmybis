<?php

namespace App\Http\Controllers;

use App\{User};
use Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'about', 'contact', 'privacy', 'terms');
        $this->middleware('seeUser');
    }

    /**
     * Show the about page.
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Show the contact page.
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Show the home page for users who haven't signed in.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check() && Auth::user()) {
        // Authenticated users default to a different page
            request()->session()->reflash();
            return redirect()->route('dashboard');
        } else {
            return view('home');
        }
    }

    /**
     * Show the Privacy Policy page.
     *
     * @return \Illuminate\Http\Response
     */
    public function privacy()
    {
        return view('privacy');
    }

    /**
     * Show the Terms and Conditions page.
     *
     * @return \Illuminate\Http\Response
     */
    public function terms()
    {
        return view('terms');
    }
}
