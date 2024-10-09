<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json($posts);
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
            'user_id' => ['exists:users,id'],
            'title' => ['required', 'min:10'],
            'type' => ['required']
        ]);

        $post = Post::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'type' => $request->type,
        ]);

        return response()->json($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['messagem' => 'Postagem não encontrada'], 404);
        }
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $request->validate([
            'user_id' => ['exists:users,id'],
            'title' => ['min:10'],
            'type' => ['']
        ]);

        if ($post ===  null) {
            return response()->json(['Erro' => 'Impossível realizar a atualização, postagem não encontrada'], 404);
        }

        $post->update([
            'user_id' => $request->user_id ?: $post->user_id,
            'title' => $request->title ?: $post->title,
            'type' => $request->type ?: $post->type,
        ]);
        $post->save();

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if ($post === null) {
            return response()->json(['Erro' => 'Impossível deletar, postagem não encontrada'], 404);
        }
        $post->delete();
        return response()->json(['messagem' => 'Postagem deletada com sucesso']);
    }
}
