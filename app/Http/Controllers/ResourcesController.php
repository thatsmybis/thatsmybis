<?php

namespace App\Http\Controllers;

use App\{User};
use Auth;
use Illuminate\Http\Request;

class ResourcesController extends Controller
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
        return view('resources.index');
    }

    /**
     * Show the pvp page.
     *
     * @return \Illuminate\Http\Response
     */
    public function pvp()
    {
        return view('resources.pvp');
    }

    /**
     * Show the pve page.
     *
     * @return \Illuminate\Http\Response
     */
    public function pve()
    {
        return view('resources.pve');
    }

    /**
     * Show the druid page.
     *
     * @return \Illuminate\Http\Response
     */
    public function druid()
    {
        return view('resources.druid');
    }

    /**
     * Show the hunter page.
     *
     * @return \Illuminate\Http\Response
     */
    public function hunter()
    {
        return view('resources.hunter');
    }

    /**
     * Show the mage page.
     *
     * @return \Illuminate\Http\Response
     */
    public function mage()
    {
        return view('resources.mage');
    }

    /**
     * Show the priest page.
     *
     * @return \Illuminate\Http\Response
     */
    public function priest()
    {
        return view('resources.priest');
    }

    /**
     * Show the rogue page.
     *
     * @return \Illuminate\Http\Response
     */
    public function rogue()
    {
        return view('resources.rogue');
    }

    /**
     * Show the shaman page.
     *
     * @return \Illuminate\Http\Response
     */
    public function shaman()
    {
        return view('resources.shaman');
    }

    /**
     * Show the warlock page.
     *
     * @return \Illuminate\Http\Response
     */
    public function warlock()
    {
        return view('resources.warlock');
    }

    /**
     * Show the warrior page.
     *
     * @return \Illuminate\Http\Response
     */
    public function warrior()
    {
        return view('resources.warrior');
    }

}
