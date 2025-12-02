<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;

class SettingsController extends Controller
{
    public function index()
    {
        return response()->json(Configuration::all()->pluck('value', 'key'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'quiz_question_limit' => 'required|integer|min:1',
            'score_per_question'  => 'required|integer|min:1',
        ]);

        foreach ($data as $key => $value) {
            Configuration::set($key, $value);
        }

        return response()->json(['message' => 'Configurações atualizadas com sucesso!']);
    }
}