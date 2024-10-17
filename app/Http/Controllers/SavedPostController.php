<?php

namespace App\Http\Controllers;

use App\Models\SavedPost;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SavedPostController extends Controller
{
    public function index(Request $request)
    {
        $savedPosts = SavedPost::where('user_id', $request->user()->id)->get();
        if ($savedPosts->isEmpty()) {
            return response()->json(['message' => 'Você não possui nenhum post salvo'], Response::HTTP_NO_CONTENT);
        }

        return response()->json(['data' => $savedPosts], Response::HTTP_OK);
    }
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['exists:users,id'],
            'post_id' => ['required', 'exists:posts,id']
        ]);

        if ($savedPost = SavedPost::where('user_id', $request->user()->id)->where('post_id', $request->post_id)->first()){

            $savedPost->delete();

            return response()->json(['messagem' => 'Este post já está salvo'], Response::HTTP_NO_CONTENT);
        }

        SavedPost::create([
            'user_id' => $request->user()->id,
            'post_id' => $request->post_id
        ]);

        return response()->json(['message' => 'Post salvo com sucesso'], Response::HTTP_OK);
    }
}
