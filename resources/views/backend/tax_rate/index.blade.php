@extends('main')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Tax Rates Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaxModal">
            <i class="bi bi-plus-circle me-1"></i> Add Tax Rate
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Rate (%)</th>
                        <th>Inclusive</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($taxRates as $key => $tax)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $tax->name }}</td>
                            <td>{{ $tax->rate_percent }}%</td>
                            <td>
                                @if ($tax->is_inclusive)
                                    <span class="badge bg-info text-dark">Inclusive</span>
                                @else
                                    <span class="badge bg-secondary">Exclusive</span>
                                @endif
                            </td>
                            <td>
                                @if ($tax->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary editTaxBtn"
                                    data-id="{{ $tax->id }}"
                                    data-name="{{ $tax->name }}"
                                    data-rate="{{ $tax->rate_percent }}"
                                    data-inclusive="{{ $tax->is_inclusive }}"
                                    data-active="{{ $tax->is_active }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('tax-rates.destroy', $tax->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this tax rate?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($taxRates->hasPages())
                <div class="mt-3">
                    {{ $taxRates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createTaxModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('tax-rates.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Tax Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rate (%)</label>
                    <input type="number" name="rate_percent" step="0.01" class="form-control" required>
                </div>

                <div class="form-check form-switch mb-3 m-4">
                    <input class="form-check-input" type="checkbox" name="is_inclusive" value="1">
                    <label class="form-check-label">Inclusive</label>
                </div>

                <div class="form-check form-switch mb-3 m-4">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                    <label class="form-check-label">Active</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editTaxModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content" id="editTaxForm">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Tax Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rate (%)</label>
                    <input type="number" name="rate_percent" id="editRate" step="0.01" class="form-control" required>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="editInclusive" name="is_inclusive" value="1">
                    <label class="form-check-label">Inclusive</label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="editActive" name="is_active" value="1">
                    <label class="form-check-label">Active</label>
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
        $('.editTaxBtn').on('click', function() {
            const id = $(this).data('id');
            $('#editTaxForm').attr('action', `/tax-rates/${id}`);

            $('#editName').val($(this).data('name'));
            $('#editRate').val($(this).data('rate'));
            $('#editInclusive').prop('checked', $(this).data('inclusive') == 1);
            $('#editActive').prop('checked', $(this).data('active') == 1);

            $('#editTaxModal').modal('show');
        });
    });
</script>
@endsection
