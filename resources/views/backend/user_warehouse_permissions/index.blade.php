@extends('main')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>User Warehouse Permissions</h2>
        <a href="{{ route('user-warehouse-permissions.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add New
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Warehouse</th>
                <th>Sell</th>
                <th>Purchase</th>
                <th>Adjust</th>
                <th>Reports</th>
                <th>Custom Price</th>
                <th width="150">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($permissions as $perm)
                <tr>
                    <td>{{ $perm->id }}</td>
                    <td>{{ $perm->user->name ?? '-' }}</td>
                    <td>{{ $perm->warehouse->name ?? '-' }}</td>
                    <td>{!! $perm->can_sell ? '✅' : '❌' !!}</td>
                    <td>{!! $perm->can_purchase ? '✅' : '❌' !!}</td>
                    <td>{!! $perm->can_adjust_stock ? '✅' : '❌' !!}</td>
                    <td>{!! $perm->can_view_reports ? '✅' : '❌' !!}</td>
                    <td>{!! $perm->can_custom_price ? '✅' : '❌' !!}</td>
                    <td>
                        <a href="{{ route('user-warehouse-permissions.edit', $perm->id) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="{{ route('user-warehouse-permissions.destroy', $perm->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this permission?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $permissions->links() }}
    </div>
</div>
@endsection
