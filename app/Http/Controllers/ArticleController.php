<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Display the articles and buttons
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Check access
        if(!Auth::check()){
            return redirect("/")->withSuccess('You are not allowed to access');
        }

        //Pagination
        $articles = Article::latest()->paginate(5);

        return view('articles.index',compact('articles'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Form to create new Article
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::check()){
            return redirect("/")->withSuccess('You are not allowed to access');
        }

        return view('articles.create');
    }

    /**
     * Save new article on DB
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::check()){
            return redirect("/")->withSuccess('You are not allowed to access');
        }

        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $input = $request->all();

        if ($image = $request->file('image')) {
            $destinationPath = 'image/';
            $articleImage = "image-".date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $articleImage);
            $input['image'] = "$articleImage";
        }

        Article::create($input);

        return redirect()->route('articles.index')
            ->with('success','Article created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        if(!Auth::check()){
            return redirect("/")->withSuccess('You are not allowed to access');
        }
        return view('articles.show',compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return view('articles.edit',compact('article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $input = $request->all();

        if ($image = $request->file('image')) {
            $destinationPath = 'image/';
            $articleImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $articleImage);
            $input['image'] = "$articleImage";
        }else{
            unset($input['image']);
        }

        $article->update($input);

        return redirect()->route('articles.index')
            ->with('success','Article updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();

        return redirect()->route('articles.index')
            ->with('success','Article deleted successfully');
    }
}
