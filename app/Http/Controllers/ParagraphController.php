<?php

namespace App\Http\Controllers;

use App\Models\Paragraph;
use App\Models\Photo;
use Illuminate\Http\Request;

class ParagraphController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paragraphs = Paragraph::all();
        $photo = Photo::all();
        return response()->json([$paragraphs, $photo]);
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
            'post_id' => ['exists:posts,id'],
            'paragraph_id' => ['exists:paragraph,id'],
            'photo' => ['image']
        ]);


        if ($request->photo) {
            $paragraph = Paragraph::create([
                'post_id' => $request->post_id
            ]);
            $photo = Photo::create([
                'paragraph_id' => $paragraph->id,
                'photo' => $request->file('photo')->store('public/photos')
            ]);
            return response()->json([$paragraph, $photo]);
        }
        $paragraph = Paragraph::create([
            'post_id' => $request->post_id,
            'subtitle' => $request->subtitle,
            'content' => $request->text
        ]);
        $paragraph->save();
        return response()->json($paragraph);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Paragraph  $paragraph
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $paragraph = Paragraph::with('photos:paragraph_id,photo')->find($id);
        if (!$paragraph) {
            return response()->json(['messagem' => 'Parágrafo não encontrado'], 404);
        }
        return response()->json($paragraph);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd([
            'photo' =>$request->file('photo'),
            'subtitle' =>$request->input('subtitle'),
        ]);
        $request->validate([
            'paragraph_id' => ['exists:paragraphs,id'],
            'photo' => ['image', 'required'],
        ]);
        $paragraph = Paragraph::find($id);

        if (!$paragraph) {
            return response()->json(['Erro' => 'Impossível realizar a atualização, postagem não encontrada'], 404);
        }

        $paragraph->update([
            'subtitle' => $request->subtitle ?: $paragraph->subtitle,
            'content' => $request->text ?: $paragraph->content

        ]);
        return response()->json($paragraph);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Paragraph  $paragraph
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $paragraph = Paragraph::find($id);
        if (!$paragraph) {
            return response()->json(['error' => 'Paragraph not found'], 404);
        }

        $paragraph->photos()->delete();

        $paragraph->delete();
        return response()->json(['messagem' => 'Parágrafo deletado com sucesso']);
    }
}
