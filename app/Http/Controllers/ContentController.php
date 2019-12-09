<?php

namespace App\Http\Controllers;

use App\{Content, Raid, User};
use Auth;
use Illuminate\Http\Request;

class ContentController extends Controller
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
     * Show the index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $content = Content::where('category', 'resource')->whereNull('removed_at')->with('user')->get();
        return view('content.index', ['content' => $content]);
    }

    /**
     * Show a content page
     *
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $content = Content::where('slug', $slug)->whereNull('removed_at')->with('user')->firstOrFail();
        return view('content.show', [
            'content'  => $content,
            'raids'    => Raid::all(),
        ]);
    }

    /**
     * Remove content
     *
     * @param int $id The id of the content to remove
     *
     * @return \Illuminate\Http\Response
     */
    public function remove($id)
    {
        $content = Content::findOrFail($id);
        $updateValues['removed_at'] = getDateTime();
        $content->update($updateValues);

        return redirect()->route('contentIndex');
    }

    /**
     * Update content
     *
     * @param int $id The id of the content to update.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id = null)
    {
        $validationRules =  [
            'content'  => 'nullable|string|max:65000',
            'id'       => 'nullable|integer|exists:content,id',
            'title'    => 'required|string|max:255',
            // 'slug'     => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ];

        $this->validate(request(), $validationRules);

        $updateValues['content'] = request()->input('content');
        $updateValues['title']   = request()->input('title');
        $updateValues['slug']    = slug(request()->input('title'));

        if ($id) {
            $content = Content::findOrFail($id);
            $category = $content->category;

            if ($category == 'news' && !Auth::user()->hasRole(env('PERMISSION_ADMIN'))) {
                abort(403, 'You cannot edit news posts.');
            }

            // ints are raid id's
            if (is_numeric($category)) {
                if (!Auth::user()->hasRole(env('PERMISSION_RAID_LEADER'))) {
                    abort(403, 'You cannot edit raid posts.');
                }

                $raid = Raid::findOrFail($category);

                $updateValues['raid_id'] = $raid->id;
                $category = $raid->slug;
            }

            $updateValues['last_edited_by'] = Auth::id();
            $content->update($updateValues);

            return redirect()->back();
        } else {
            $updateValues['user_id'] = Auth::id();
            $category = request()->input('category');

            if ($category == 'news' && !Auth::user()->hasRole(env('PERMISSION_ADMIN'))) {
                abort(403, 'You cannot create news posts.');
            }

            // ints are raid id's
            if (is_numeric($category)) {
                if (!Auth::user()->hasRole(env('PERMISSION_RAID_LEADER'))) {
                    abort(403, 'You cannot create raid posts.');
                }

                $raid = Raid::findOrFail($category);

                $updateValues['raid_id'] = $raid->id;
                $category = $raid->slug;
            }

            $updateValues['category'] = $category;

            $content = Content::create($updateValues);
            return redirect()->route('showContent', ['slug' => $content->slug]);
        }
    }

}
