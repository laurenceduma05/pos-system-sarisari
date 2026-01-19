<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\ChordTransposer;

class SongController extends Controller
{
    public function index(Request $request)
    {
        // $status = $request->query('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $perPage = $request->query('per_page', 5);

        $query = Song::query();

        if ($startDate && $endDate) {
            $query->whereBetween(\DB::raw('DATE(create_at)'), [$startDate, $endDate]);

        }

        $songs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'songs'   => $songs,
            'pagination' => [
            'total' => $songs->total(),
            'current_page' => $songs->currentPage(),
            'last_page' => $songs->lastPage(),
            'per_page' => $songs->perPage(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'by' => 'required|string|max:255',
            'lyrics' => 'required|string',
            'chords' => 'required|string',
            'key' => 'required|string',
            'link' => 'nullable|url',
        ]);

        Song::create($request->all());

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function edit(Song $song)
    {
        return $song;
    }

    public function update(Request $request, Song $song)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'lyrics' => 'required|string',
            'chords' => 'required|string',
            'key' => 'required|string',
        ]);

        $song->update($request->all());

        return response()->json(['success' => true]);
    }

    public function destroy(Song $song)
    {
        $song->delete();
        return redirect()->route('admin.songs.index');
    }

    public function transpose(Request $request, ChordTransposer $transposer)
    {
        $currentKey = 'C';  // For now, assume the current key is C, or fetch from DB
        $targetKey = $request->input('key');  // Get the key from the frontend

        // Fetch the song lyrics (this would come from the DB in a real-world scenario)
        $lyrics = "A D F G A this is the day of celebration, this is the day unto rejoice"; // Sample lyrics

        // Transpose the lyrics
        $transposedLyrics = $transposer->transposeLyrics($lyrics, $currentKey, $targetKey);

        return response()->json(['transposed_lyrics' => $transposedLyrics]);
    }
}
