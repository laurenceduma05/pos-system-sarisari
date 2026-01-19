<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Teaching;


class TeachingController extends Controller
{
    public function index(Request $request)
    {
        $query = Teaching::query();

        $perPage = $request->query('per_page', 15);

        $teachings = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return response()->json([
            'teachings'   => $teachings,
            'pagination' => [
            'total' => $teachings->total(),
            'current_page' => $teachings->currentPage(),
            'last_page' => $teachings->lastPage(),
            'per_page' => $teachings->perPage(),
            ]
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'descriptions' => 'nullable|string',
            'link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Allow pdf, doc, docx files
            'audience' => 'required|array', // Audience must be an array
            'archive' => 'nullable|boolean',
        ]);

        \Log::info('Request data:', $request->all());

        // Handle the file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('teachings', 'public'); // Store the file in 'public/teachings'
        }

        $uploaderId = auth()->id();

        if (!$uploaderId) {
            return response()->json(['message' => 'User is not authenticated'], 401);
        }

        // Create a new teaching record
        Teaching::create([
            'uploader' => $uploaderId, // Assuming uploader is the authenticated user
            'title' => $validatedData['title'],
            'descriptions' => $validatedData['descriptions'],
            'link' => $validatedData['link'],
            'file_path' => $filePath, // Save the file path
            'file_type' => $file ? $file->getMimeType() : null, // Get the MIME type
            'audience' => $validatedData['audience'],
            'archive' => $validatedData['archive'] ?? false, // Default to false if not provided
        ]);

        return response()->json(['message' => 'Teaching created successfully']);
    }
}
