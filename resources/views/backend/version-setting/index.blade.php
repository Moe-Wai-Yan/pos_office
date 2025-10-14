@extends('main')

@section('content')
    <div class="card my_card">
    <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
        <h5 class="mb-0 text-dark">Version Settings - <div class="badge badge-myColor" id="badgeTotalCount">{{ App\Models\VersionSetting::count() }}</div></h5>
    </div>

        <div class="card-body px-0">
            <div class="table-responsive">
                <table class="table table-hover" id="datatable" style="width:100%;">
                    <thead class="text-center text-muted" style="background: #F3F6F9">
                        <th class="text-center no-sort">Android Version</th>
                        <th class="text-center no-sort">IOS Version</th>
                        <th class="text-center no-sort">Release Note</th>
                        <th class="text-center no-sort no-search">ပြင်မည်</th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
      $(document).ready(function() {
            let table = $('#datatable').DataTable( {
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "/version-setting/datatable/ssd",
                language : {
                  "processing": "<img src='{{asset('/images/loading.gif')}}' width='50px'/><p></p>",
                  "paginate" : {
                    "previous" : '<i class="mdi mdi-chevron-triple-left"></i>',
                    "next" : '<i class="mdi mdi-chevron-triple-right"></i>',
                  }
                },
                columns : [
                  {data: 'android_version', name: 'android_version' , class: 'text-center'},
                  {data: 'ios_version', name: 'ios_version' , class: 'text-center'},
                  {data: 'release_note', name: 'release_note' , class: 'text-center'},
                  {data: 'action', name: 'action', class: 'text-center'},
                ],
                columnDefs : [
                  {
                    targets : 'hidden',
                    visible : false,
                    searchable : false,
                  },
                  {
                    targets : 'no-sort',
                    sortable : false,
                  },
                  {
                    targets : 'no-search',
                    searchable : false,
                  },
                  {
                    targets: [0],
                    class : "control"
                  },
                ]
            });

            const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 1800,
              width : '18em',
              timerProgressBar: true,
              didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
            })
        })
    </script>
@endsection
