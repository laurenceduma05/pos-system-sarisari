<?php

namespace App\Http\Controllers\Admin;

use App\Models\ChequeIssuances;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Quotas;
use Carbon\Carbon;

class ChequeIssuancesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $status = $request->query('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $perPage = $request->query('per_page', 5);

        $query = ChequeIssuances::query();

        if ($startDate && $endDate) {
            // $query->whereBetween('created_at', [$startDate, $endDate]);
            // $query->whereBetween(\DB::raw('DATE(created_at)'), [$startDate, $endDate]); // filter by issuance
            $query->whereBetween(\DB::raw('DATE(due_date)'), [$startDate, $endDate]); // filter by due
            
        }

        $currentDate = Carbon::today()->toDateString(); // Today’s date (YYYY-MM-DD)
        //query to check if due_date is bequal to or less than the current date
        $query->selectRaw('*, IF(status = "Payables" AND DATE(due_date) <= ?, 1, 0) as is_due_or_past_due', [$currentDate]);

        $cheques = $query->orderBy('created_at', 'desc')->paginate($perPage);
        
        return response()->json([
            'cheques'   => $cheques,
            'pagination' => [
            'total' => $cheques->total(),
            'current_page' => $cheques->currentPage(),
            'last_page' => $cheques->lastPage(),
            'per_page' => $cheques->perPage(),
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $amount = $request->input('amount');
        $dueDate = $request->input('due_date');

        $dueDate = Carbon::parse($dueDate);

        $quota = Quotas::getQuotaByDueDate($dueDate);

        // $quota = Quotas::getStartOfDayQuota();
        // If there's no quota for that due date, create a new quota
        if (!$quota) {
            $quota = Quotas::create([
                'quota_limit' => 40000, // Set the global weekly limit (e.g., 40,000 cheques)
                'issued' => 0,
                // 'day_start' => now(), // Set the start of the week (Monday, Sunday, etc.)
                'day_start'    => $dueDate,
            ]);
        }
        
        if ($quota->isQuotaExceeded($amount)) {
            return response()->json(['message' => 'Quota limit reached for this week.'], 400);
        }

        $validate = request()->validate([
            'business_name' => 'required',
            'bank'          => 'required',
            'cheque_number' => 'required',
            'amount'        => 'required',
            'status'        => 'required',
            'remarks'       => 'nullable',
            'due_date'      => 'required',
        ]);

        ChequeIssuances::create([
            'business_name' => $validate['business_name'],
            'bank'          => $validate['bank'],
            'cheque_number' => $validate['cheque_number'],
            'amount'        => $validate['amount'],
            'status'        => $validate['status'],
            'remarks'       => $validate['remarks'],
            'due_date'      => $validate['due_date'],
        ]);
        
        // Update the quota issued amount
        $quota->increment('issued', $amount);

        return response()->json([
            'message' => 'success',
        ]);
    }

    public function edit(ChequeIssuances $cheque)
    {
        return $cheque;
    }
   
    public function update(Request $request, ChequeIssuances $cheque)
    {

        $previousAmount = $cheque->amount;
        $previousDueDate = $cheque->due_date;

        $validated = $request->validate([
            'business_name' => 'required',
            'bank'          => 'required',
            'cheque_number' => 'required',
            'amount'        => 'required',
            'status'        => 'required',
            'remarks'       => 'nullable',
            'due_date'      => 'required',
        ]);

        $newAmount = $request->input('amount');
        $newDueDate = $request->input('due_date');

        $previousDueDate = Carbon::parse($previousDueDate);
        $newDueDate = Carbon::parse($newDueDate);

        if ($previousDueDate != $newDueDate) {
            // Reduce the quota for the previous due_date
            $previousQuota = Quotas::getQuotaByDueDate($previousDueDate);
            if ($previousQuota) {
                $previousQuota->decrement('issued', $previousAmount);
            }

            // Check and update the quota for the new due_date
            $newQuota = Quotas::getQuotaByDueDate($newDueDate);
            if (!$newQuota) {
                $newQuota = Quotas::create([
                    'quota_limit' => 40000, // Set the global quota limit
                    'issued'      => 0,     // Start with 0 issued amount
                    'day_start'   => $newDueDate, // Use the new due_date as the day_start
                ]);
            }

            // Check if the quota is exceeded for the new due date
            if ($newQuota->isQuotaExceeded($newAmount)) {
                return response()->json(['message' => 'Quota limit reached for this new due date.'], 400);
            }

            // Update the quota for the new due date
            $newQuota->increment('issued', $newAmount);
        }

        // Now, update the cheque record itself
        $cheque->update([
            'business_name' => $validated['business_name'],
            'bank'          => $validated['bank'],
            'cheque_number' => $validated['cheque_number'],
            'amount'        => $newAmount,
            'status'        => $validated['status'],
            'remarks'       => $validated['remarks'],
            'due_date'      => $newDueDate,
        ]);

        return response()->json([
            'message' => 'Cheque updated successfully.',
            'data'    => $cheque,
        ]);
    }

    public function updatePayables()
    {
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay(); # get tomorrow's date

        # Get all cheques where due_date is less than or equal to today and status is not 'Paid'
        $cheques = ChequeIssuances::where('due_date', '<=', $today->toDateString())
            ->where('status', '!=', 'Paid')
            ->get();

        foreach ($cheques as $cheque) {
            # Update due_date to tomorrow and set status to 'payables'
            $cheque->update([
                'due_date' => $tomorrow->toDateString(), # set to tomorrow
                'status'   => 'Payables', # set status to payables
            ]);
        }

        return response()->json(['success' => true], 200);
    }

    public function destroy(ChequeIssuances $cheque)
    {
        $cheque->delete();
        return response()->json(['success' => true], 200);
    }

    public function getTotalAmountDueToday() {
        $currentDate = Carbon::now();
        $today = $currentDate->toDateString();
        $totalDueToday = ChequeIssuances::whereDate('due_date', $today)->where('status', 'payables')->get();
        $issuedQuota = $totalDueToday->sum('amount');
        
        if ($issuedQuota) {
            return response()->json([
                'data' => $issuedQuota ?? null,
            ]);
        }

        return response()->json([
            'message' => 'No data available',
        ]);
    }

    public function getTotalAmountIssuedToday() {
        $currentDate = Carbon::now();
        $today = $currentDate->toDateString();
        $totalToday = ChequeIssuances::whereDate('created_at', $today)->get();
        $issuedQuota = $totalToday->sum('amount');
        
        if ($issuedQuota) {
            return response()->json([
                'data' => $issuedQuota ?? null,
            ]);
        }

        return response()->json([
            'message' => 'No data available',
        ]);
    }
    
}
