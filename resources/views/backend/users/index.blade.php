@extends('main')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">User Management</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="bi bi-plus-circle me-1"></i> Add User
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $key => $user)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach ($user->roles as $role)
                                        <span class="badge bg-info text-dark">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if ($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary editUserBtn" data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                        data-active="{{ $user->is_active }}" data-roles='@json($user->roles->pluck('name'))'>
                                        <i class="bi bi-pencil"></i>
                                    </button>


                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this user?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if ($users->hasPages())
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('users.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="col-md-6 d-flex align-items-center mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Roles</label>
                        <select name="roles[]" class="form-select" multiple required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple roles.</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" class="modal-content" id="editUserForm">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" name="password" id="editPassword" class="form-control">
                    </div>

                    <div class="col-md-6 d-flex align-items-center mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editActive" name="is_active"
                                value="1">
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Roles</label>
                        <select name="roles[]" id="editRoles" class="form-select" multiple required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
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
            $('.editUserBtn').on('click', function() {
                const id = $(this).data('id');
                $('#editUserForm').attr('action', `/users/${id}`);

                $('#editName').val($(this).data('name'));
                $('#editEmail').val($(this).data('email'));
                $('#editPassword').val('');
                $('#editActive').prop('checked', $(this).data('active') == 1);

                // Parse roles
                const roles = $(this).data('roles');
                $('#editRoles').val(roles).trigger('change');

                $('#editUserModal').modal('show');
            });
        });
    </script>
@endsection
