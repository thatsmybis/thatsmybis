<?php

namespace App\Http\Controllers\Api;

use App\{User};
use Auth;
use Illuminate\Http\Request;

class ProfileController extends Controller
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
     * Update a user's officer note
     *
     * @param int $id The id of the user to update.
     *
     * @return \Illuminate\Http\Response
     */
    public function submitOfficerNote($id = null)
    {
        $validationRules =  [
            'officer_note'  => 'nullable|string|max:5000',
        ];

        $this->validate(request(), $validationRules);

        $user = User::findOrFail($id);

        if ($user->id == Auth::id()) {
            $updateValues['officer_note'] = request()->input('officer_note');

            $user->update($updateValues);
        }

        return redirect()->route('showUser', ['id' => $user->id, 'username' => $user->username]);
    }
}
