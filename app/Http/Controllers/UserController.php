<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve all users
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' =>['required','string','max:255', 'unique:users'],
            'email' =>['required','string','email','max:255','unique:users'],
            'password' => ['string','min:8'],
            'is_admin' => ['required']
        ]);

        // Create a new user with the validated data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password? bcrypt($request->password) : null,
            'is_admin' => $request->is_admin,
        ]);
        return response()->json($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Retrieve the user with the given ID
        $user = User::find($id);
        if (!$user) {
            return response()->json(['messagem' => 'Usuário não encontrado'], 404);
        }
        return response()->json($user);
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
        $request->validate([
            'name' =>['string','max:255', Rule::unique('users')->ignore($user->id)],
            'email' =>['string','email','max:255',Rule::unique('users')->ignore($user->id)],
            'is_admin' => ['required']
        ]);

        if ($user ===  null) {
            return response()->json(['Erro' => 'Impossível realizar a atualização, usuário não encontrado'], 404);
        }
        
        $user ->update([
            'name' => $request->name ?: $user->name,
            'email' => $request->email ?: $user->email,
            'is_admin' => $request->is_admin ?: $user->is_admin
        ]);

        $user->save();
        return response()->json($user);

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
        if ($user === null) {
            return response()->json(['Erro' => 'Impossível deletar, usuário não encontrado'], 404);
        }
        $user->delete();
        return response()->json(['messagem' => 'Usuário deletado com sucesso']);
    }
}
