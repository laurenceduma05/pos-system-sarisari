<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Quotas;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class QuotasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 5);

        $query = Quotas::query();

        $quotas = $query->paginate($perPage);

        return response()->json([
            'quotas'   => $quotas,
                'pagination' => [
                'total' => $quotas->total(),
                'current_page' => $quotas->currentPage(),
                'last_page' => $quotas->lastPage(),
                'per_page' => $quotas->perPage(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $validatedData = request()->validate([
            'quota_limit'   => 'required|numeric|min:0', //Ensure it's a positive number
            'day_start'    => 'required|date', //  Ensure it's a valid date
        ]);

        Quotas::create([
            'quota_limit'   => $validatedData['quota_limit'],
            'day_start'    => $validatedData['day_start'],
        ]);

        return response()->json([
            'message' => 'success',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Quotas $quota)
    {
        return $quota;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Quotas $quota)
    {
        $validated = request()->validate([
            'quota_limit'   => 'required',
            'day_start'    => 'required',
        ]);

        $existingQuota = Quotas::whereDate('day_start', $validated['day_start'])
            ->where('id', '!=', $quota->id)
            ->first();

        if ($existingQuota) {
            return response()->json(['error' => 'Quota already exists for this date'], 400);
        }

        $quota->update($validated);

        return response()->json(['success' => true]);
    }

    public function isQoutaFull(Quotas $quota) {
        $currentDate = Carbon::now();
        $weekStart = $currentDate->startOfWeek()->toDateString();
        $weekEnd = $currentDate->endOfWeek()->toDateString();
        $quotasForWeek = Quotas::whereBetween('day_start', [$weekStart, $weekEnd])->get();
        $quotaLimit = $quotasForWeek->sum('quota_limit');
        $usedQuota = $quotasForWeek->sum('issued');

        if ($usedQuota >= $quotaLimit) {
            return response()->json(['message' => 'Quota is full for this week.'], 400);
        }

        return response()->json(['message' => 'Quota is not full for this week.'], 200);
    }

    public function isDailyQuotaFull(Quotas $quota)
    {
        $currentDate = Carbon::now();
        $today = $currentDate->toDateString();
        $quotasForToday = Quotas::whereDate('day_start', $today)->get(); // Assuming 'created_at' stores the date for each quota record

        $quotaLimit = $quotasForToday->sum('quota_limit');
        $usedQuota = $quotasForToday->sum('issued');
        if ($usedQuota >= $quotaLimit) {
            return response()->json(['message' => 'Quota is full for today.'], 400);
        }
        return response()->json(['message' => 'Quota is not full for today.'], 200);
    }

    public function getQuotaIssued(Quotas $quota)
    {
        $currentDate = Carbon::now();
        $today = $currentDate->toDateString();
        $quotasForToday = Quotas::whereDate('day_start', $today)->get();
        $issuedQuota = $quotasForToday->sum('issued');
        
        if ($issuedQuota) {
            return response()->json([
                'data' => $issuedQuota ?? null,
            ]);
        }

        return response()->json([
            'message' => 'No data available',
        ]);
    }

    public function getAvailableQuota()
    {
        $currentDate = Carbon::now();
        $today = $currentDate->toDateString();
        // $quotasForToday = Quotas::whereDate('created_at', $today)->get();
        $quotasForToday = Quotas::whereDate('day_start', $today)->get();
        // return self::whereDate('day_start', '=', now()->toDateString())
        // ->first();
        $issuedQuota = $quotasForToday->sum('issued');
        $quotaLimit = $quotasForToday->sum('quota_limit');

        $availableQuota = $quotaLimit - $issuedQuota;

        if ($quotasForToday->isEmpty()) {
            return response()->json([
                'message' => 'No quota data available for today.'
            ], 400);
        }
        
        if ($issuedQuota >= $quotaLimit) {
            return response()->json([
                'message' => 'No available quota for today'
            ]);
        }

        return response()->json([
            'available_quota' => $availableQuota,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quotas $quota)
    {
        $quota->delete();
        return response()->json(['success' => true], 200);
    }
}
