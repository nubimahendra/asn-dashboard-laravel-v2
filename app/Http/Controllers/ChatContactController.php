<?php

namespace App\Http\Controllers;

use App\Models\ChatContact;
use Illuminate\Http\Request;

class ChatContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contacts = ChatContact::latest()->paginate(10);
        return view('admin.chat.contacts.index', compact('contacts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20|unique:chat_contacts,number',
        ]);

        ChatContact::create([
            'name' => $request->name,
            'number' => $request->number,
            'remote_id' => $request->number . '@s.whatsapp.net', // Default WA format
        ]);

        return redirect()->route('admin.chat.contacts.index')->with('success', 'Kontak berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChatContact $contact)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'number' => 'required|string|max:20|unique:chat_contacts,number,' . $contact->id,
        ]);

        $contact->update([
            'name' => $request->name,
            'number' => $request->number,
            // Don't update remote_id blindly if it might be different, but for now:
            'remote_id' => $request->number . '@s.whatsapp.net',
        ]);

        return redirect()->route('admin.chat.contacts.index')->with('success', 'Kontak berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatContact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.chat.contacts.index')->with('success', 'Kontak berhasil dihapus');
    }
}
