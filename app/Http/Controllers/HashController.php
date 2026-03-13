<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HashController extends Controller
{
    public function save(Request $request)
    {
        // ハッシュ値をセッションに保存
        $request->session()->put('current_hash', $request->input('hash'));
        return response()->json(['status' => 'success']);
    }
}
