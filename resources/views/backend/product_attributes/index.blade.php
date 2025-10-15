@extends('main')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Product Attributes Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
            <i class="bi bi-plus-circle me-1"></i> Add Attribute
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productAttributes as $key => $supplier)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary editSupplierBtn"
                                    data-id="{{ $supplier->id }}"
                                    data-name="{{ $supplier->name }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('product-attributes.destroy', $supplier->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this Attribute?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($productAttributes->hasPages())
                <div class="mt-3">
                    {{ $productAttributes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Supplier Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('product-attributes.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Attribute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content" id="editSupplierForm">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Attribute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.editSupplierBtn').on('click', function() {
            const id = $(this).data('id');
            $('#editSupplierForm').attr('action', `/product-attributes/${id}`);

            $('#editName').val($(this).data('name'));

            $('#editSupplierModal').modal('show');
        });
    });
</script>
@endsection
