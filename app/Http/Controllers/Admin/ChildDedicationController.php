<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChildDedicationRequest;

use App\Models\ChildDedication;
use Illuminate\Http\Request;

class ChildDedicationController extends Controller
{
    public function index(Request $request)
    {
        $query = ChildDedication::query();

        $perPage = $request->query('per_page', 15);

        $childDedications = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json([
            'child_dedications'   => $childDedications,
            'pagination' => [
                'total' => $childDedications->total(),
                'current_page' => $childDedications->currentPage(),
                'last_page' => $childDedications->lastPage(),
                'per_page' => $childDedications->perPage(),
            ]
        ]);
    }
    public function store(StoreChildDedicationRequest $child_request)
    {
        $requests = $child_request->validated();
        $prepared = [];
        foreach ($requests as $key => $value) { // need to loop because of sponsors that has array value
            if ($key == 'sponsors') {
                $prepared[$key] = json_encode($value);
            }
        }
        $data = array_merge($requests, $prepared);
        ChildDedication::create($data);

        return response()->json(['message' => 'Child dedication record created successfully']);
    }

    public function edit(ChildDedication $dedication)
    {
        return $dedication;
    }

    public function update(ChildDedication $dedication, StoreChildDedicationRequest $child_request)
    {
        $child_request = $child_request->validated();
        $dedication->update($child_request);

        return response()->json(['success' => true]);
    }
}
