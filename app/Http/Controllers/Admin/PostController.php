<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('updated_at', 'DESC')->orderBy('created_at', 'DESC')->get();
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $post = new Post();
       $categories = Category::all();
        return view('admin.posts.create', compact('post', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=> 'required|string|unique:posts',
            'content'=> 'required|string',
            'image'=> 'nullable|url',
            'category_id'=> 'nullable|exists:categories,id'
        ],
        
        [
            'title.required'=> 'Il titolo è obbligatorio',
            'content.required'=> 'Devi scrivere il contenuto del post',
            'title.unique'=> 'Esiste già un post dal titolo $request->title',
            'image.url'=> 'Url dell\'immagine non valido',
            'category_id.exists'=>'Non esiste una categoria associabile'
        ]);

        $data = $request->all();

        $post = new Post();

        $post-> fill($data);

        $post-> slug = Str::slug($post->title, '-');

        $post->save();

        return redirect()->route('admin.posts.show', $post)
               ->with('message', 'Post creato con successo')
               ->with('type', 'success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $data = $request->all();

     
        $data['slug'] = Str::slug($data['title'], '-');

        $post-> update($data);

        return redirect()->route('admin.posts.show', $post)
               ->with('message', 'Post modificato con successo')
               ->with('type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('admin.posts.index')
        ->with('message', 'il post è stato eliminato con successo')
        ->with('type', 'danger');
    }
}
