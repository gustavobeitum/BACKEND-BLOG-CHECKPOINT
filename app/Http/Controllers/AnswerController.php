<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Comment;
use App\Notifications\ForUsersNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $answers = Answer::all();

        return response()->json(['data' => $answers], Response::HTTP_OK);
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
            'user_id' => ['exists:users,id', 'integer'],
            'comment_id' => ['exists:comments,id', 'integer'],
            'response' => ['max:100'],
        ]);
        $answer = Answer::create([
            'user_id' => $request->user()->id,
            'comment_id' => $request->comment_id,
            'response' => $request->response,
        ]);

        $comment = Comment::findOrFail($request->comment_id);

        $comment->user->notify(new ForUsersNotification($answer, $comment));

        return response()->json(['data' => $answer], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $answer = Answer::find($id);
        if (!$answer) {
            return response()->json(['messagem' => 'Resposta não encontrada'], Response::HTTP_NO_CONTENT);
        }

        return response()->json(['data' => $answer], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'response' => ['max:100'],
        ]);
        $answer = Answer::find($id);
        if (!$answer) {
            return response()->json(['Erro' => 'Impossível realizar a atualização, postagem não encontrada'], Response::HTTP_NO_CONTENT);
        }

        if ($request->user()->id == $answer->user_id) {
            $answer->response = $request->response;
            $answer->save();

            return response()->json(['data' => $answer], Response::HTTP_OK);
        }
        return response()->json(['mensagem' => 'Você não possui permissão para alterar essa resposta'], Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $answer = Answer::find($id);
        if (!$answer) {
            return response()->json(['messagem' => 'Resposta não encontrada'], Response::HTTP_NO_CONTENT);
        }

        if ($request->user()->id == $answer->user_id || $request->user()->is_admin == 'admin') {
            DB::table('notifications')->where('data->answer_id', $answer->id)->delete();

            $answer->delete();

            return response()->json(['messagem' => 'Resposta excluída com sucesso']);
        }
        return response()->json(['messagem' => 'Você não possui permissão para deletar este comentário'], Response::HTTP_NO_CONTENT);
    }
}
