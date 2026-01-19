<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class EventController extends Controller
{
    public function index(Request $request)
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $perPage = $request->query('per_page', 7);

            $query = Event::query();

            if ($startDate && $endDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }

            $events = $query->orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'events' => $events->items(),
                'pagination' => [
                    'total' => $events->total(),
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Event index error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

        public function store(Request $request)
        {
            Log::info('Store method called', $request->all());

            try {
                $validator = Validator::make($request->all(), [
                    'title' => 'required|string|max:255',
                    'by' => 'required|string|max:255',
                    'details' => 'required|string',
                    'link' => 'nullable|url',
                    'start_time' => 'required|date',
                    'end_time' => 'required|date|after:start_time',
                ]);

                if ($validator->fails()) {
                    Log::error('Validation failed', $validator->errors()->toArray());
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => $validator->errors()
                    ], 422);
                }

                Log::info('Validation passed, creating event');

                $event = Event::create($request->all());

                Log::info('Event created successfully', ['event_id' => $event->id]);

                return response()->json([
                    'message' => 'Event created successfully',
                    'event' => $event
                ], 201);

            } catch (\Exception $e) {
                Log::error('Event store error: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                return response()->json([
                    'error' => 'Server error: ' . $e->getMessage()
                ], 500);
            }
        }

    public function edit($id)
    {
        try {
            $event = Event::findOrFail($id);
            return response()->json($event);
        } catch (\Exception $e) {
            Log::error('Event edit error: ' . $e->getMessage());
            return response()->json(['error' => 'Event not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'by' => 'required|string|max:255',
                'details' => 'required|string',
                'link' => 'nullable|url',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
            ]);

            $event = Event::findOrFail($id);
            $event->update($request->all());

            return response()->json([
                'message' => 'Event updated successfully',
                'success' => true
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Event update error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->delete();

            return response()->json([
                'message' => 'Event deleted successfully',
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Event destroy error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}
