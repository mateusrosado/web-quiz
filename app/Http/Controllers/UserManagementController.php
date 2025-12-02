<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    public function index()
    {
        return User::orderBy('id', 'desc')->paginate(20);
    }

    public function toggleAdmin(Request $request, $id)
    {
        if ($request->user()->id == $id) {
            return response()->json(['message' => 'Você não pode alterar seu próprio status.'], 403);
        }

        $user = User::findOrFail($id);
        $user->is_admin = !$user->is_admin;
        $user->save();

        return response()->json([
            'message' => 'Status de administrador atualizado.', 
            'is_admin' => $user->is_admin
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user()->id == $id) {
            return response()->json(['message' => 'Você não pode se excluir.'], 403);
        }

        User::destroy($id);
        return response()->json(['message' => 'Usuário excluído com sucesso.']);
    }
}