<?php

namespace App\Http\Controllers;

use App\Models\Method;
use Illuminate\Http\Request;

class MethodController extends Controller
{
    public function index()
    {
        $methods = Method::orderBy('id', 'desc')->get();
        return view('method', compact('methods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $method = Method::create($validated);

        if ($request->expectsJson() || $request->isXmlHttpRequest()) {
            return response()->json([
                'success' => true,
                'message' => 'Metode berhasil ditambahkan.',
                'method' => $method
            ]);
        }

        return redirect()->back()->with('success', 'Metode berhasil ditambahkan.');
    }
}
