@extends('main')
<!-- Bootstrap Icons CDN -->

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Warehouses Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWarehouseModal">
            <i class="bi bi-plus-circle me-1"></i> Add Warehouse
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($warehouses as $key => $warehouse)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $warehouse->name }}</td>
                        <td>{{ $warehouse->phone }}</td>
                        <td>{{ $warehouse->email }}</td>
                        <td>{{ $warehouse->country }}</td>
                        <td>{{ $warehouse->city }}</td>
                        <td>
                            @if ($warehouse->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary editWarehouseBtn"
                                data-id="{{ $warehouse->id }}"
                                data-name="{{ $warehouse->name }}"
                                data-phone="{{ $warehouse->phone }}"
                                data-email="{{ $warehouse->email }}"
                                data-country="{{ $warehouse->country }}"
                                data-city="{{ $warehouse->city }}"
                                data-active="{{ $warehouse->is_active }}">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this warehouse?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Warehouse Modal -->
<div class="modal fade" id="createWarehouseModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('warehouses.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Add Warehouse</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Country</label>
            <input type="text" name="country" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" required>
        </div>

        <div class="col-md-6 d-flex align-items-center mt-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                <label class="form-check-label">Active</label>
            </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Warehouse Modal -->
<div class="modal fade" id="editWarehouseModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content" id="editWarehouseForm">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Warehouse</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body row g-3">
        <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="editName" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" id="editPhone" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="editEmail" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Country</label>
            <input type="text" name="country" id="editCountry" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">City</label>
            <input type="text" name="city" id="editCity" class="form-control" required>
        </div>

        <div class="col-md-6 d-flex align-items-center mt-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="editActive" name="is_active" value="1">
                <label class="form-check-label">Active</label>
            </div>
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
    $('.editWarehouseBtn').on('click', function() {
        const id = $(this).data('id');
        $('#editWarehouseForm').attr('action', `/warehouses/${id}`);

        $('#editName').val($(this).data('name'));
        $('#editPhone').val($(this).data('phone'));
        $('#editEmail').val($(this).data('email'));
        $('#editCountry').val($(this).data('country'));
        $('#editCity').val($(this).data('city'));

        // Handle active toggle
        const active = $(this).data('active') == 1;
        $('#editActive').prop('checked', active);

        $('#editWarehouseModal').modal('show');
    });
  });
</script>
@endsection
