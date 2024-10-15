<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

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
        return response()->json(['date' => $posts], Response::HTTP_OK);
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
            'title' => ['required'],
            'type' => ['required'],
            'image' => ['required', 'file', 'image'],
            'description' => ['required']
        ]);

        if ($request->hasFile('image')) {
            $images = $request->file('image');
            $images_url = $images->store('post/images', 'public');
        } else {
            $images_url = null;
        }

        $post = Post::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'type' => $request->type,
            'image' => $images_url,
            'description' => $request->description
        ]);

        return response()->json(['data' => $post], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::with('comments.answers')->find($id);
        if (!$post) {
            return response()->json(['messagem' => 'Postagem não encontrada'], Response::HTTP_NO_CONTENT);
        }
        return response()->json(['data' => $post], Response::HTTP_OK);
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
            'image' => ['file', 'image']
        ]);

        if (!$post) {
            return response()->json(['Erro' => 'Impossível realizar a atualização, postagem não encontrada'], Response::HTTP_NO_CONTENT);
        }
        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $image = $request->file('image');
            $image_url = $image->store('post/images', 'public');
        } else {
            $image_url = $post->image;
        }

        $post->update([
            'user_id' => $request->user_id ?: $post->user_id,
            'title' => $request->title ?: $post->title,
            'type' => $request->type ?: $post->type,
            'image' => $image_url,
            'description' => $request->description ?: $post->description
        ]);
        $post->save();

        return response()->json(['data' => $post], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::with('comments.answers')->find($id);
        if (!$post) {
            return response()->json(['Erro' => 'Impossível deletar, postagem não encontrada'], Response::HTTP_NO_CONTENT);
        }
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        foreach ($post->paragraphs as $paragraph) {
            foreach ($paragraph->photos as $photo) {
                Storage::disk('public')->delete($photo->photo);
                $photo->delete();
            }
            $paragraph->delete();
        }

        $post->delete();
        
        return response()->json(['messagem' => 'Postagem deletada com sucesso'], Response::HTTP_OK);
    }
}
