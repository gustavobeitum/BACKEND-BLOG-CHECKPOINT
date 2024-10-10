<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;
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

        return response()->json($answers, 200);
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
            'response' => ['string'],
        ]);
        $answer = Answer::create([
            'user_id' => $request->user_id,
            'comment_id' => $request->comment_id,
            'response' => $request->response,
        ]);

        return response()->json($answer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $answer = Answer::with('comment:id,user_id,post_id,comment,created_at')->select('id','comment_id','user_id','response')->find($id);
            if (!$answer){
                return response()->json(['messagem' => 'Resposta não encontrada'], Response::HTTP_NO_CONTENT);
            }

        return response()->json($answer, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Answer $answer)
    {
        $request->validate([
            'response' => ['string'],
        ]);

        $answer->response = $request->response;
        $answer->save();

        return response()->json($answer, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Answer  $answer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $answer = Answer::find($id);
        if($answer === null){
            return response()->json(['messagem' => 'Resposta não encontrada'], 404);
        }
        $answer->delete();

        return response()->json(['messagem' => 'Resposta excluída com sucesso']);
    }
}
