@extends('admin.layouts.app')
@section('page_title')
    {{(!empty($page_title) && isset($page_title)) ? $page_title : ''}}
@endsection
@push('head-scripts')
    <link rel="stylesheet" href="{{ asset('admin/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin/select2/dist/css/select2-bootstrap5.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin/datatable/datatables.min.css') }}" rel="stylesheet" />
@endpush
@section('content')
    <div class="card mt-3">
        <div class="card-body">
            {{-- Start: Page Content --}}
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="card-title mb-0">{{(!empty($p_title) && isset($p_title)) ? $p_title : ''}}</h4>
                    <div class="small text-medium-emphasis">{{(!empty($p_summary) && isset($p_summary)) ? $p_summary : ''}}</div>
                </div>
                <div class="btn-toolbar d-none d-md-block" role="toolbar" aria-label="Toolbar with buttons">
                    @can('admin_user-management_role-create')
                        <a href="{{(!empty($url) && isset($url)) ? $url : ''}}" class="btn btn-sm btn-primary">{{(!empty($url_text) && isset($url_text)) ? $url_text : ''}}</a>
                    @endcan
                </div>
            </div>
            <hr>
            {{-- Datatatble : Start --}}
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="indextable" class="table table-bordered table-striped table-hover table-responsive w-100 pt-1">
                            <thead class="table-dark">
                            <th>#</th>
                            <th>Name</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            {{-- Datatatble : End --}}
            {{-- Page Description : Start --}}
            @if(!empty($p_description) && isset($p_description))
                <div class="card-footer">
                    <div class="row">
                        <div class="col-12 mb-sm-2 mb-0">
                            <p>{{(!empty($p_description) && isset($p_description)) ? $p_description : ''}}</p>
                        </div>
                    </div>
                </div>
            @endif
            {{-- Page Description : End --}}
            {{-- Delete Confirmation Model : Start --}}
            <div class="del-model-wrapper">
                <div class="modal fade" id="del-model" tabindex="-1" aria-labelledby="del-model" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn-close shadow-none" data-coreui-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="font-weight-bold mb-2"> Are you sure you wanna delete this ?</p>
                                <p class="text-muted "> This item will be deleted immediately. You can't undo this action.</p>
                            </div>
                            <div class="modal-footer">
                                <form method="POST" id="del-form">
                                    @csrf
                                    {{method_field('DELETE')}}
                                    <button type="button" class="btn btn-light" data-coreui-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Delete Confirmation Model : End --}}
            {{-- End: Page Content --}}
        </div>
    </div>
@endsection
@push('footer-scripts')
    <script src="{{ asset('admin/select2/dist/js/select2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/datatable/datatables.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            //Datatable
            $('#indextable').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                order: [[ 0, "desc" ]],
                ajax: {
                    "type":"GET",
                    "url":"{{route('admin.get.role')}}",
                    // "data":function (d){
                    //     d.group_id = document.getElementById('group-id').value;
                    // }
                },
                columns: [
                    { data: 'role_id'},
                    { data: 'name' },
                    { data: 'perm_arr' },
                    { data: null},
                ],
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false,
                        searchable: false,
                        width: '100px',
                        render: function ( data, type, row, meta ) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        targets: 1,
                        width: '15%'
                    },
                    {
                        targets: 2,
                        orderable: false,
                        className:'perm_col',
                        // defaultContent: '<span class="badge bg-info text-dark">group</span>'
                        render: function ( data, type, row, meta ) {
                            var output="";
                            for(var i=0;i<data.length;i++){
                                output +=  '<span class="badge bg-secondary">'+data[i]+'</span>' ;
                                if(i< data.length-1){
                                    output +=" ";
                                }
                            }
                            return output;
                        }
                    },
                    {
                        targets: -1,
                        searchable: false,
                        orderable: false,
                        render: function ( data, type, row, meta ) {
                            let URL = "{{ route('admin.role.show', ':id') }}";
                            URL = URL.replace(':id', row.role_id);
                            return '<div class="d-flex">' +
                                @can('admin_user-management_role-show')
                                    '<a class="me-1" href="'+URL+'"><span class="badge bg-success text-dark">Show</span>' +
                                @endcan
                                    @can('admin_user-management_role-edit')
                                    '<a class="me-1" href="'+URL+'/edit"><span class="badge bg-info text-dark">Edit</span></a>' +
                                @endcan
                                    @can('admin_user-management_role-delete')
                                    '<a class="me-1" href="javascript:void(0)"><span type="button" class="badge bg-danger" data-url="'+URL+'" data-coreui-toggle="modal" data-coreui-target="#del-model">Delete</span></a>'+
                                @endcan
                                    '</div>'

                        }
                    }
                ]
            });
        });
        function selectRange(){
            $('.dataTable').DataTable().ajax.reload()
        }
    </script>
    {{-- Delete Confirmation Model : Script : Start --}}
    <script>
        $("#del-model").on('show.coreui.modal', function (event) {
            var triggerLink = $(event.relatedTarget);
            var url = triggerLink.data("url");
            $("#del-form").attr('action', url);
        })
    </script>
    {{-- Delete Confirmation Model : Script : Start --}}
    {{-- Toastr : Script : Start --}}
    @if(Session::has('messages'))
        <script>
            noti({!! json_encode((Session::get('messages'))) !!});
        </script>
    @endif
    {{-- Toastr : Script : End --}}
@endpush
