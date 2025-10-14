@extends('main')

@section('content')
<div class="container">
    <h2 class="mb-3">Edit User Warehouse Permission</h2>

    <form action="{{ route('user-warehouse-permissions.update', $userWarehousePermission->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
    <label for="user_id" class="form-label">User</label>
    <select name="user_id" class="form-select" required>
        <option value="">Select User</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}"
                {{ old('user_id', $userWarehousePermission->user_id ?? '') == $user->id ? 'selected' : '' }}>
                {{ $user->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="warehouse_id" class="form-label">Warehouse</label>
    <select name="warehouse_id" class="form-select" required>
        <option value="">Select Warehouse</option>
        @foreach($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}"
                {{ old('warehouse_id', $userWarehousePermission->warehouse_id ?? '') == $warehouse->id ? 'selected' : '' }}>
                {{ $warehouse->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" name="can_sell" value="1"
                {{ old('can_sell', $userWarehousePermission->can_sell ?? false) ? 'checked' : '' }}>
            <label class="form-check-label">Can Sell</label>
        </div>

        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" name="can_purchase" value="1"
                {{ old('can_purchase', $userWarehousePermission->can_purchase ?? false) ? 'checked' : '' }}>
            <label class="form-check-label">Can Purchase</label>
        </div>

        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" name="can_adjust_stock" value="1"
                {{ old('can_adjust_stock', $userWarehousePermission->can_adjust_stock ?? false) ? 'checked' : '' }}>
            <label class="form-check-label">Can Adjust Stock</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" name="can_view_reports" value="1"
                {{ old('can_view_reports', $userWarehousePermission->can_view_reports ?? false) ? 'checked' : '' }}>
            <label class="form-check-label">Can View Reports</label>
        </div>

        <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" name="can_custom_price" value="1"
                {{ old('can_custom_price', $userWarehousePermission->can_custom_price ?? false) ? 'checked' : '' }}>
            <label class="form-check-label">Can Custom Price</label>
        </div>
    </div>
</div>


        <div class="mt-3">
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('user-warehouse-permissions.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
