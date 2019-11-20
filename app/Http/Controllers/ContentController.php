<?php

namespace App\Http\Controllers;

use App\{Content, User};
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
        $content = Content::where('is_news', 0)->whereNull('removed_at')->with('user')->get();
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
            'slug'     => 'required|string|max:255',
            'is_news'  => 'nullable|boolean',
        ];

        $this->validate(request(), $validationRules);

        $updateValues['content'] = request()->input('content');
        $updateValues['title']   = request()->input('title');
        $updateValues['slug']    = request()->input('slug');
        $updateValues['is_news'] = request()->input('is_news') ? 1 : 0;

        if ($id) {
            $content = Content::findOrFail($id);
            $updateValues['last_edited_by'] = Auth::id();
            $content->update($updateValues);

            return redirect()->back();
        } else {
            $updateValues['user_id'] = Auth::id();
            $content = Content::create($updateValues);
            return redirect()->route('showContent', ['slug' => $content->slug]);
        }
    }

}
