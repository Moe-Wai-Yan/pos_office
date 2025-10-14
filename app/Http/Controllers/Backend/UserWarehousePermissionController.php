<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserWarehousePermission;

class UserWarehousePermissionController extends Controller
{
     public function __construct()
    {
        // Only users with global permission can manage warehouse permissions
        // $this->middleware(['permission:view_reports'])->only('index');
        // $this->middleware(['permission:adjust_stock'])->only(['create', 'edit', 'store', 'update']);
    }

    public function index()
    {
        $permissions = UserWarehousePermission::with(['user', 'warehouse'])->paginate(10);
        return view('backend.user_warehouse_permissions.index', compact('permissions'));
    }

    public function create()
    {
        $users = User::all();
        $warehouses = Warehouse::all();
        return view('backend.user_warehouse_permissions.create', compact('users', 'warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        UserWarehousePermission::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'warehouse_id' => $request->warehouse_id,
            ],
            [
                'can_sell' => $request->has('can_sell'),
                'can_purchase' => $request->has('can_purchase'),
                'can_adjust_stock' => $request->has('can_adjust_stock'),
                'can_view_reports' => $request->has('can_view_reports'),
                'can_custom_price' => $request->has('can_custom_price'),
            ]
        );

        return redirect()->route('user-warehouse-permissions.index')
                         ->with('success', 'Permission saved successfully!');
    }

    public function edit(UserWarehousePermission $userWarehousePermission)
    {
        $users = User::all();
        $warehouses = Warehouse::all();
        return view('backend.user_warehouse_permissions.edit', compact('userWarehousePermission', 'users', 'warehouses'));
    }

    public function update(Request $request, UserWarehousePermission $userWarehousePermission)
    {
        $userWarehousePermission->update([
            'can_sell' => $request->has('can_sell'),
            'can_purchase' => $request->has('can_purchase'),
            'can_adjust_stock' => $request->has('can_adjust_stock'),
            'can_view_reports' => $request->has('can_view_reports'),
            'can_custom_price' => $request->has('can_custom_price'),
        ]);

        return redirect()->route('user-warehouse-permissions.index')
                         ->with('success', 'Permission updated successfully!');
    }

    public function destroy(UserWarehousePermission $userWarehousePermission)
    {
        $userWarehousePermission->delete();

        return redirect()->route('user_warehouse_permissions.index')
                         ->with('success', 'Permission deleted successfully!');
    }
}
