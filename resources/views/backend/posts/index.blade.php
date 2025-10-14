@extends('main')

@section('content')
    <div class="card my_card">
        <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
            <h5 class="mb-0 text-dark">Posts - <div class="badge badge-myColor" id="badgeTotalCount">
                    {{ App\Models\Post::count() }}</div>
            </h5>
            <a class="primary_button" href="{{ route('post.create') }}">
                <div class="d-flex align-items-center">
                    <i class=" ri-add-circle-fill mr-2 primary-icon"></i>
                    <span class="button_content">Post အသစ်ဖန်တီးမည်</span>
                </div>
            </a>
        </div>
        <div class="card-body px-0">
            <div class="table-responsive">
                <table class="table" id="datatable" style="width:100%;">
                    <thead class="text-center bg-light text-muted">
                        <th class="text-center no-sort no-search">Poster</th>
                        <th class="text-center no-sort">Description</th>
                        <th class="text-center no-sort no-search">ပြင်မည်/ဖျက်မည်</th>
                    </thead>
                </table>
            </div>
        </div>
        <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="commentModalLabel">Comments</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Description</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="comment-list">

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1800,
            width: '18em',
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
        $(document).ready(function() {
            let table = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "/posts/datatable/ssd",
                language: {
                    "processing": "<img src='{{ asset('/images/loading.gif') }}' width='50px'/><p></p>",
                    "paginate": {
                        "previous": '<i class="mdi mdi-chevron-triple-left"></i>',
                        "next": '<i class="mdi mdi-chevron-triple-right"></i>',
                    }
                },
                columns: [{
                        data: 'poster',
                        name: 'poster',
                        class: 'text-center'
                    },
                    {
                        data: 'description',
                        name: 'description',
                        class: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        class: 'text-center'
                    },
                ],
                columnDefs: [{
                        targets: 'hidden',
                        visible: false,
                        searchable: false,
                    },
                    {
                        targets: 'no-sort',
                        sortable: false,
                    },
                    {
                        targets: 'no-search',
                        searchable: false,
                    },
                    {
                        targets: [0],
                        class: "control"
                    },
                ]
            });

            $(document).on('click', '.delete_btn', function(e) {
                e.preventDefault();
                swal({
                        text: "Are you sure?",
                        icon: "info",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            let id = $(this).data('id');
                            $.ajax({
                                url: `/posts/${id}/delete`,
                                method: 'DELETE',
                            }).done(function(res) {
                                let totalCount = $("#badgeTotalCount").text();
                                $('#badgeTotalCount').text(totalCount - 1);
                                table.ajax.reload();
                                Toast.fire({
                                    icon: 'success',
                                    title: "အောင်မြင်ပါသည်။"
                                })
                            })
                        }
                    });
            })
        })

        let commentListContainer = document.getElementById('comment-list')

        function fetchComments(id) {

            commentListContainer.innerHTML = '';
            $.ajax({
                method: 'GET',
                url: `/post-comments/${id}`,
                success: function(res) {
                    res.forEach(comment => {
                        let tr = document.createElement('tr')
                        tr.dataset.id = comment.id
                        tr.innerHTML = `<td>
                                        ${comment.body}
                                        </td>
                                        <td>
                                            <button onclick="deleteComment(${comment.id})" class="btn btn-sm btn-danger"><i class="ri-delete-bin-line"></i></button>
                                        </td>`
                        commentListContainer.appendChild(tr)
                    });
                     $('#commentModal').modal('show');
                }
            })
        }

        function deleteComment(id) {
            swal({
                    text: "Deleting comments can result in deleting replies of this comment. Are you sure?",
                    icon: "info",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: `/comments/${id}`,
                            method: 'DELETE',
                        }).done(function(res) {
                            if (res == 'success') {
                                // commentListContainer.querySelector(`tr[data-id="${id}"]`).remove()
                                Toast.fire({
                                    icon: 'success',
                                    title: "အောင်မြင်ပါသည်။"
                                })
                                location.reload()
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: "မအောင်မြင်ပါ။"
                                })
                            }
                        })
                    }
                });
        }
    </script>
@endsection
