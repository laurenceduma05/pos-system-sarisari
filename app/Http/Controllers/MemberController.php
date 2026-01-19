<?php

namespace App\Http\Controllers;

use App\Http\Requests\Members as RequestsMembers;
use App\Models\Members;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Members::query();

        $perPage = $request->query('per_page', 15);

        $membersRecord = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return response()->json([
            'members_record'   => $membersRecord,
            'pagination' => [
                'total' => $membersRecord->total(),
                'current_page' => $membersRecord->currentPage(),
                'last_page' => $membersRecord->lastPage(),
                'per_page' => $membersRecord->perPage(),
            ]
        ]);
    }
    public function store(RequestsMembers $member_request)
    {
        $requests = $member_request->validated();
        $prepared = [];
        foreach ($requests as $key => $value) {
            if ($key == 'sponsors') {
                $prepared[$key] = json_encode($value);
            }
        }
        $data = array_merge($requests, $prepared);
        Members::create($data);

        return response()->json(['message' => 'Member record created successfully']);
    }

    public function edit(Members $member)
    {
        return $member;
    }

    public function update(Members $member, RequestsMembers $member_request)
    {
        $member_request = $member_request->validated();
        $member->update($member_request);

        return response()->json(['success' => true]);
    }
}
