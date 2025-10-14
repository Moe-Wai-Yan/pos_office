@extends('main')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Roles Management</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
            <i class="bi bi-plus-circle me-1"></i> Add Role
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $key => $role)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary editRoleBtn"
                                data-id="{{ $role->id }}"
                                data-name="{{ $role->name }}"
                                data-permissions="{{ $role->permissions->pluck('name')->join(',') }}">
                                <i class="bi bi-pencil"></i>
                            </button>

                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this role?')">
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

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('roles.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Add Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Role Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <!-- make this real submit -->
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>


<script>
function checkAllPermission() {
    $('.permission-checkbox').prop('checked', true);
}

function uncheckAllPermission() {
    $('.permission-checkbox').prop('checked', false);
}
</script>



<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" class="modal-content" id="editRoleForm">
      @csrf @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Edit Role</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label class="form-label">Role Name</label>
            <input type="text" name="name" id="editRoleName" class="form-control" required>
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


<script>
  document.addEventListener('DOMContentLoaded', function() {
    $('.editRoleBtn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const perms = $(this).data('permissions').split(',');

        $('#editRoleName').val(name);
        $('.edit-permission').prop('checked', false);

        perms.forEach(p => {
            $(`.edit-permission[value="${p.trim()}"]`).prop('checked', true);
        });

        $('#editRoleForm').attr('action', `/roles/${id}`);
        $('#editRoleModal').modal('show');
    });
});
</script>

