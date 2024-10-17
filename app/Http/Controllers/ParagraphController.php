<?php

namespace App\Http\Controllers;

use App\Models\Paragraph;
use App\Models\Photo;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ParagraphController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paragraphs = Paragraph::with('photos')->get();
        if (!$paragraphs) {
            return response()->json(['messagem' => 'Paragrafos não encontrados'], Response::HTTP_NO_CONTENT);
        }
        return response()->json(['data' => $paragraphs], Response::HTTP_OK);
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
            'photo' => ['array'],
            'photo.*' => ['file', 'image']
        ]);

        $paragraph = Paragraph::create([
            'post_id' => $request->post_id,
            'subtitle' => $request->subtitle,
            'content' => $request->text
        ]);


        if ($request->hasFile('photo')) {
            foreach ($request->file('photo') as $photo) {
                $image_url = $photo->store('photos', 'public');
                Photo::create([
                    'paragraph_id' => $paragraph->id,
                    'photo' => $image_url
                ]);
            }
        }

        return response()->json(['data' => $paragraph->load('photos')], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Paragraph  $paragraph
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $paragraph = Paragraph::with('photos')->find($id);
        if (!$paragraph) {
            return response()->json(['messagem' => 'Parágrafo não encontrado'], Response::HTTP_NO_CONTENT);
        }
        return response()->json(['data' => $paragraph], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'paragraph_id' => ['exists:paragraphs,id'],
            'post_id' => ['exists:posts,id'],
            'photo' => ['sometimes', 'array'],
            'photo.*' => ['file', 'image']
        ]);

        $paragraph = Paragraph::find($id);

        if (!$paragraph) {
            return response()->json(['Erro' => 'Impossível realizar a atualização, postagem não encontrada'], Response::HTTP_NO_CONTENT);
        }

        $post = Post::find($paragraph->post_id);

        if ($post->user_id == $request->user()->id) {
            $paragraph->update([
                'subtitle' => $request->subtitle ?: $paragraph->subtitle,
                'content' => $request->text ?: $paragraph->content

            ]);

            if ($request->hasFile('photo')) {
                foreach ($paragraph->photos as $photo) {
                    Storage::disk('public')->delete($photo->photo);
                    $photo->delete();
                }
                foreach ($request->file('photo') as $newPhoto) {
                    $image_url = $newPhoto->store('photos', 'public');

                    Photo::create([
                        'paragraph_id' => $paragraph->id,
                        'photo' => $image_url
                    ]);
                }
            }
            return response()->json(['data' => $paragraph->load('photos')], Response::HTTP_OK);
        }
        return response()->json(['mensagem' => 'Você não tem permissão para realizar esta ação']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Paragraph  $paragraph
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $paragraph = Paragraph::find($id);
        if (!$paragraph) {
            return response()->json(['Erro' => 'Paragrafo não encontrado'], Response::HTTP_NO_CONTENT);
        }

        $post = Post::find($paragraph->post_id);

        if ($post->user_id == $request->user()->id) {
            foreach ($paragraph->photos as $photo) {
                Storage::disk('public')->delete($photo->photo);
                $photo->delete();
            }

            $paragraph->delete();

            return response()->json(['messagem' => 'Parágrafo deletado com sucesso']);
        }
        return response()->json(['mensagem' => 'Você não tem permissão para realizar esta ação'], Response::HTTP_NO_CONTENT);
    }
}
