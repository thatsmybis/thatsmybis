<?php

namespace App\Http\Controllers;

use App\{User};
use Auth;
use Illuminate\Http\Request;

class RecipesController extends Controller
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
     * Show the index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recipes');
    }

}
