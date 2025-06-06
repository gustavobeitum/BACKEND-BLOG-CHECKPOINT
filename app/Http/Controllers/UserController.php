<?php

namespace App\Http\Controllers;

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users], Response::HTTP_OK);
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
            'name' => ['required', 'string', 'max:40'],
            'last_name' => ['required', 'string', 'max:30'],
            'username' => ['required', 'string', 'max:25', 'unique:users'],
            'image' => ['file', 'image'],
            'birthday' => ['required', 'date'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => ['in:admin,normal']
        ]);

        if ($request->hasFile('image')) {
            $images = $request->file('image');
            $images_url = "storage/".$images->store('images', 'public');
        } else {
            $images_url = null;
        }

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'image' => $images_url,
            'birthday' => $request->birthday,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'is_admin' => $request->is_admin?: 'normal'
        ]);

        return response()->json(['data' => $user], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['messagem' => 'Usuário não encontrado'], Response::HTTP_NO_CONTENT);
        }
        return response()->json(['data' => $user], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['messagem' => 'Impossível realizar atualização, usuário não encontrado'], Response::HTTP_NO_CONTENT);
        }

        $request->validate([
            'username' => ['string', 'max:25', Rule::unique('users')->ignore($user->id)],
            'image' => ['file', 'image'],
            'email' => ['string', 'email', 'max:90', Rule::unique('users')->ignore($user->id)],
            'is_admin' => ['in:admin,normal']
        ]);

        if ($request->hasFile('image')) {
            if ($user->image) {
                $imagePath = str_replace('storage/', '', $user->image);
                Storage::disk('public')->delete($imagePath);
            }
            $image = $request->file('image');
            $image_url = "storage/".$image->store('images', 'public');
        } else {
            $image_url = $user->image;
        }

        $user->update([
            'username' => $request->username ?: $user->username,
            'image' => $image_url,
            'email' => $request->email ?: $user->email,
            'is_admin' => $request->is_admin ?: $user->is_admin
        ]);

        return response()->json(['data' => $user], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['messagem' => 'Impossível deletar, usuário não encontrado'], Response::HTTP_NO_CONTENT);
        }

        foreach ($user->posts as $post) {
            foreach ($post->paragraphs as $paragraph) {
                foreach ($paragraph->photos as $photo) {
                    Storage::disk('public')->delete($photo->photo);
                    $photo->delete();
                }
                $paragraph->delete();
            }
            if ($post->image) {
                $imagePath = str_replace('storage/', '', $post->image);
                Storage::disk('public')->delete($imagePath);
            }
            $post->delete();
        }
        if ($user->image) {
            $imagePath = str_replace('storage/', '', $user->image);
            Storage::disk('public')->delete($imagePath);
        }

        $user->comments()->delete();
        $user->answers()->delete();
        $deleted = $user->delete();

        if ($deleted) {
            return response()->json(['messagem' => 'Usuário deletado com sucesso']);
        } else {
            return response()->json(['messagem' => 'Erro ao deletar o usuário'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
