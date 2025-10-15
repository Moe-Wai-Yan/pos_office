@extends('main')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Units Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUnitModal">
            <i class="bi bi-plus-circle me-1"></i> Add Unit
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Base Ratio</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($units as $key => $unit)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $unit->code }}</td>
                            <td>{{ $unit->name }}</td>
                            <td>{{ $unit->base_ratio }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary editUnitBtn"
                                    data-id="{{ $unit->id }}"
                                    data-code="{{ $unit->code }}"
                                    data-name="{{ $unit->name }}"
                                    data-base="{{ $unit->base_ratio }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this unit?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($units->hasPages())
                <div class="mt-3">
                    {{ $units->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Unit Modal -->
<div class="modal fade" id="createUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('units.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Base Ratio</label>
                    <input type="number" step="0.01" name="base_ratio" class="form-control" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Unit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="editUnitForm">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Code</label>
                    <input type="text" name="code" id="editCode" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Base Ratio</label>
                    <input type="number" step="0.01" name="base_ratio" id="editBase" class="form-control" required>
                </div>
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
        $('.editUnitBtn').on('click', function() {
            const id = $(this).data('id');
            $('#editUnitForm').attr('action', `/units/${id}`);

            $('#editCode').val($(this).data('code'));
            $('#editName').val($(this).data('name'));
            $('#editBase').val($(this).data('base'));

            $('#editUnitModal').modal('show');
        });
    });
</script>
@endsection
