<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $faqs = Faq::latest()->paginate(10);
        return view('admin.chat.faqs.index', compact('faqs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'keywords' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string',
        ]);

        Faq::create([
            'question' => $request->question,
            'keywords' => $request->keywords,
            'answer' => $request->answer,
            'category' => $request->category ?? 'umum',
            'is_active' => true,
        ]);

        return redirect()->route('admin.chat.faqs.index')->with('success', 'FAQ berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'keywords' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $faq->update([
            'question' => $request->question,
            'keywords' => $request->keywords,
            'answer' => $request->answer,
            'category' => $request->category ?? 'umum',
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.chat.faqs.index')->with('success', 'FAQ berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.chat.faqs.index')->with('success', 'FAQ berhasil dihapus');
    }
}
