<?php

namespace App\Http\Controllers;

use App\{Item};
use Auth;
use Illuminate\Http\Request;

class ItemController extends Controller
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
     * Show an item
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::where('item_id', $id)->with(['wishlistUsers', 'receivedUsers'])->firstOrFail();

        return view('item', [
            'item' => $item,
        ]);
    }
}
