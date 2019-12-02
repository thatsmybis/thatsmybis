<?php

namespace App\Http\Controllers;

use App\{Raid, User};
use Auth;
use Illuminate\Http\Request;
use RestCord\DiscordClient;
use Kodeine\Acl\Models\Eloquent\Permission;

class RaidsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'seeUser']);
    }

    /**
     * Show the raids page.
     *
     * @return \Illuminate\Http\Response
     */
    public function raids()
    {
        $raids = Raid::with('role')->get();
        return view('guild.raids', [
            'raids' => $raids,
        ]);
    }
}
