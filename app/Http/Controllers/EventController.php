<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    // Ambil semua event
    public function index()
    {
        return response()->json(Event::all());
    }

    // Tambah event baru
    public function store(Request $request)
    {
        $event = Event::create($request->all());
        return response()->json($event);
    }

    // Update event
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update($request->all());
        return response()->json($event);
    }

    // Hapus event
    public function destroy($id)
    {
        Event::findOrFail($id)->delete();
        return response()->json(['message' => 'Event deleted']);
    }
}
