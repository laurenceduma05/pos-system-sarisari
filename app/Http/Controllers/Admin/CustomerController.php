<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted', false);
        $search = $request->query('search');
        
        $query = Customer::withCount('orders');
        
        if ($showDeleted) {
            $query = $query->onlyTrashed();
        }
        
        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->paginate(15);
        
        return response()->json($customers);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();
        
        $customer = Customer::create($validated);
        
        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        $customer->load('orders');
        $customer->total_purchases = $customer->totalPurchases();
        $customer->available_credit = $customer->availableCredit();
        
        return response()->json($customer);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $validated = $request->validated();
        
        $customer->update($validated);
        
        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => $customer,
        ]);
    }

    /**
     * Remove the specified customer from storage (soft delete).
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        
        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }

    /**
     * Restore a soft-deleted customer.
     */
    public function restore($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();
        
        return response()->json([
            'message' => 'Customer restored successfully',
            'data' => $customer,
        ]);
    }

    /**
     * Get customer's orders.
     */
    public function orders(Customer $customer)
    {
        $orders = $customer->orders()->latest()->paginate(10);
        
        return response()->json($orders);
    }
}
