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
        return view('content.show', ['content' => $content]);
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

            if ($content->category == 'news' && !Auth::user()->hasRole('admin|guild_master|officer')) {
                abort(403);
            }

            if (in_array($category, explode(',', env('RAID_SLUGS'))) && !Auth::user()->hasRole('admin|guild_master|officer|raid_leader')) {
                abort(403);
            }

            if (in_array()) {

            }

            $updateValues['last_edited_by'] = Auth::id();
            $content->update($updateValues);

            return redirect()->back();
        } else {
            $updateValues['user_id'] = Auth::id();
            $category = request()->input('category');

            if ($category == 'news' && !Auth::user()->hasRole('admin|guild_master|officer|raider')) {
                abort(403);
            }

            // ints are raid id's
            if (is_numeric($category)) {
                if (!Auth::user()->hasRole('admin|guild_master|officer|raid_leader|raider')) {
                    abort(403);
                }

                $raid = Raid::findOrFail($category);

                $updateValues['raid_id'] = $raid->id;
                $category = $raid->name;
            }

            $updateValues['category'] = $category;

            $content = Content::create($updateValues);
            return redirect()->route('showContent', ['slug' => $content->slug]);
        }
    }

}
