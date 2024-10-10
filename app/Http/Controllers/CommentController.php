<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Comment::all();
        return response()->json($comments, 200);
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
            'post_id' => ['exists:posts,id'],
            'comment' => ['min:3']
        ]);
        
        $comment = Comment::create([
            'user_id' => $request->user_id,/*()->*/
            'post_id' => $request->post_id,
            'comment' => $request->comment
        ]);

        return response()->json($comment, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['messagem' => 'Comentario não encontrado'], 404);
        }
        return response()->json($comment, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'comment' => ['min:3']
        ]);

        $comment->comment = $request->comment;
        $comment->save();

        return response()->json($comment, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $comment = Comment::find($id);
        if ($comment === null) {
            return response()->json(['Erro' => 'Impossível deletar, comentário não encontrado'], 404);
        }
        
        $comment->delete();
        return response()->json(['messagem' => 'Comentário deletado com sucesso']);
    }
}
