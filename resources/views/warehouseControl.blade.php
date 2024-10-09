<!DOCTYPE html>
<html lang="en" data-bs-theme="dark" class="bg-cover-1">

<head>
    <meta charset="utf-8" />
    <title>SMTT | Warehouse Control</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="icon" type="image/png" href="{{asset('img/company.png')}}">
    <link href="{{asset('css/vendor.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/app.min.css')}}" rel="stylesheet" />

    <link href="{{asset('plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
    <link href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet" />

</head>

<body>

<div id="app" class="app app-sidebar-collapsed">
        <x-topbar />
        <x-slidebar />

        <button class="app-sidebar-mobile-backdrop" data-toggle-target=".app"
            data-toggle-class="app-sidebar-mobile-toggled"></button>
        <div id="content" class="app-content">
            <div class="d-flex align-items-center mb-3">
                <div class="flex-fill">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">SMTT</a></li>
                        <li class="breadcrumb-item">Warehouse Control</li>
                        {{-- <li class="breadcrumb-item active">Profile</li> --}}
                    </ul>
                    <h1 class="page-header mb-0"> Warehouse Control</h1>
                </div>
                <div class="ms-auto">
                    <a href="#" class="btn btn-outline-theme" data-bs-toggle="modal" data-bs-target="#modal"
                    onclick="modal('{{route('addWarehouse')}}', 2)"><i class="fa fa-plus-circle me-1"></i> Add Warehouse</a> 
                </div>
            </div>
           
            <div class="row gx-4">
                <div class="col-lg-12">
                    <form id="searchForm">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label"></label>
                                    
                                </div>
                            </div>
                           
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Search</label>
                                    <div class="input-group flex-nowrap">
                                        <input type="text" class="form-control" name="name"
                                            placeholder="WAREHOUSE NAME" autofocus/>
                                        <button type="submit" class="btn btn-theme input-group-text"><i
                                                class="bi bi-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hljs-container rounded-bottom">
                            <!-- html -->
                            <div class="h-50px">
                                Toggle column: 
                                <a class="btn btn-outline-theme btn-sm toggle-vis" data-column="1" style="border-radius: 4px;">ID</a> - 
                                <a class="btn btn-outline-theme btn-sm toggle-vis" data-column="2" style="border-radius: 4px;">Warehouse</a> - 
                                <a class="btn btn-outline-theme btn-sm toggle-vis" data-column="3" style="border-radius: 4px;">Created By</a> - 
                                <a class="btn btn-outline-theme btn-sm toggle-vis" data-column="4" style="border-radius: 4px;">Updated By</a>
                            </div>

                            <table id="myDataTable" class="table table-responsive w-100">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Warehouse</th>
                                        <th>Created By</th>
                                        <th>Updated By</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="card-arrow">
                            <div class="card-arrow-top-left"></div>
                            <div class="card-arrow-top-right"></div>
                            <div class="card-arrow-bottom-left"></div>
                            <div class="card-arrow-bottom-right"></div>
                        </div>
                    </div>
                </div>

            </div>
          
            <div class="modal fade" id="modal" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-scrollable modal-xl">
                    <div class="modal-content" id="modalContent">
                        <div class="text-center">
                            <div class="spinner-border" style="width: 5rem; height: 5rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div> 
                    </div> 
                </div>
            </div>
  
            <div class="toast-container position-fixed bottom-0 end-0 p-3">
                <div id="liveToast" class="toast text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" id="toastMessage">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>

        <x-rocket_loader />

        <a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>

        <script src="{{asset('js/jquery-3.6.3.min.js')}}"></script>
        <script src="{{asset('js/moment.min.js')}}"></script>
        <script src="{{asset('js/vendor.min.js')}}"></script>
        <script src="{{asset('js/app.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
</body>

</html>

<script>
var datatable_data =[];

function format(d) {
        return (
            '<table class="table table-bordered w-75 m-auto child-table text-center" id="child-table-' + d.id + '">' +
            '<thead>' +
            '<tr>' +
            '<th class="text-center">Username</th>' +
            '<th class="text-center">Name</th>' +
            '<th class="text-center">Email</th>' +
            '<th class="text-center">Action</th>'+
            '</tr>' +
            '</thead>' +
            '</table>'
        );
}

$(document).ready(function() {
    
    datatable =    $('#myDataTable').DataTable({
            'processing': true,
            'serverSide': true,
            'searching': false,
            'ordering': false,
            'paging': true,      // Enable pagination
            'lengthMenu': [10, 25, 50, 100],  // Set length menu options
            "ajax": {
                    method: "POST",
                    url: '{{route("getWarehouseData")}}',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.name = datatable_data.name;
                    }
                },
            "dom": '<"top"<"row"<"col-sm-1"l><"col-sm-6"rf><"col-sm-5"p>>>rt<"bottom"<"row"<"col-sm-4"i><"col-sm-8"p>>>',
            "language": {
            "lengthMenu": " _MENU_ entries per page",
            "zeroRecords": "No matching records found",
            },
            'columns': [
                {
                    'className': 'dt-control',
                    'orderable': false,
                    'data': null,
                    'defaultContent': ''
                },
                { 'data': 'id' },
                { 'data': 'name' },
                { 'data': 'created_by' },
                { 'data': 'updated_by' },
                {
                    mRender: function (data, type, row) {
                        $id = row['id'];

                        return '<center><button class="btn btn-dark btn-sm me-1 dropdown">'    
                        +'<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</a>'   
                        +'<div class="dropdown-menu">'
                        +'<a href="#" class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('getNotWarehouseUser') }}' + '\', \'' + $id + '\')"><i class="bi bi-person"></i> Add User</a>'        
                        +'<a href="#" class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('editWarehouse') }}' + '\', \'' + $id + '\')"><i class="bi bi-pencil-square"></i> Edit</a>'    
                        +'<a href="#" class="dropdown-item" onClick="btnRemove(\''+$id +'\')"><i class="bi bi-trash"></i> Delete</a>'    
                        +'</div> '    
                        +'</button></center>'  
                    },
                },
            ],
            
        });

        $('#myDataTable tbody').on('click', 'td.dt-control', function() {
        var tr = $(this).closest('tr');
        var row = datatable.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            // $(this).html('<i class="bi bi-plus-circle"></i>');
        } else {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
            // $(this).html('<i class="bi bi-dash-circle"></i>');

            tr.attr('data-parent-id', row.data().id);

            // Initialize child DataTable
            $('#child-table-' + row.data().id).DataTable({
                'processing': true,
                'serverSide': true,
                'searching': false,
                'ordering': false,
                'paging':false,
                "bInfo" : false,
                'ajax': {
                    'url': '{{route("getChildData")}}',
                    'type': 'POST',
                    'data': {
                        '_token': '{{ csrf_token() }}',
                        'parentId': row.data().id
                    }
                },
                'columns': [
                    { 'data': 'username' },
                    { 'data': 'name' },
                    { 'data': 'email_address' },
                    { 
                        mRender: function (data, type, row) {
                        $userId = row['id'];
                        $warehouseId = tr.data('parent-id');

                        return '<center><button class="btn btn-dark btn-sm me-1 dropdown">'    
                        +'<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</a>'   
                        +'<div class="dropdown-menu">' 
                        +'<a href="#" class="dropdown-item" onClick="removeChildUser(\''+$userId +'\',\''+$warehouseId +'\')"><i class="bi bi-trash"></i> Remove</a>'    
                        +'</div> '    
                        +'</button></center>'  
                        }, 
                    },
                ],
            });
        }
    });

    document.querySelectorAll('a.toggle-vis').forEach((el) => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            let columnIdx = e.target.getAttribute('data-column');
            let column = datatable.column(columnIdx);
            column.visible(!column.visible());
        });
    });
});

// function modal(mode,id) {  //mode directy changes to routes
   
//     $.ajax({
//         method: "POST",
//         url: '{{route("addWarehouse")}}',
//         dataType: 'json',
//         data: { 
//             mode: mode,
//             id: id,
//             _token: '{{ csrf_token() }}' // Include the CSRF token
//         },
//         success: function (d) {
//             $('#modalContent').html(d.data); 
//             // $.getScript(d.java);
//         },
//         error: function (xhr, status, error) {
//             alert('Error: ' + error.message);
//             $("#modal").modal('hide');
//         }
//     });
// }

// function changemodal() {
   
//    $.ajax({
//        type: "GET",
//        url: '{{route("changeWarehouse")}}',
//        dataType: 'json',
//        data: { 
//            _token: '{{ csrf_token() }}' // Include the CSRF token
//        },
//        success: function (d) {
//            $('#modalContent').html(d.data); 
//            // $.getScript(d.java);
//        },
//        error: function (xhr, status, error) {
//            alert('Error: ' + error.message);
//            $("#modal").modal('hide');
//        }
//    });
// }

function btnRemove(id) {
    $.ajax({
        type: "POST",
        url: '{{route("deleteWarehouse")}}',
        dataType: 'json',
        data: { 
            id: id,
            _token: '{{ csrf_token() }}' // Include the CSRF token
        },
        success: function (d) {
            if (d.status === 2) {
                toast(2, d.message);
            } else {
                toast(1, d.message);
            }
            datatable.ajax.reload();
        },
        error: function (xhr, status, error) {
            alert('Error: ' + error.message);
        }
    });
}

function removeChildUser(userId, warehouseId){
    $.ajax({
        method: "POST",
        url : '{{route('removeWarehouseUser')}}',
        dataType: 'json',
        data:{
            userId: userId,
            warehouseId: warehouseId,
            _token: '{{ csrf_token() }}'
        },
        success: function (d){
            if(d.status === 2){
                toast(2, d.message);
                datatable.ajax.reload();
            }else{
                toast(1, d.message);
            }
        },
        error: function(xhr, status, error){
            alert('Error: '+ error.message);
        }
    })
}

$("#searchForm").submit(function(event) {
    
    event.preventDefault();
    
    // Serialize the form data
    let searchFormData = $(this).serialize();

    datatable_data.name = $(this).find('[name="name"]').val();
  
    datatable.ajax.reload();
});

function toast(status, message) {
        if (status === 1) {
            $("#liveToast").removeClass().addClass("toast text-bg-danger border-0");
        } else if (status === 2) {
            $("#liveToast").removeClass().addClass("toast text-bg-success border-0");
        } else {
            $("#liveToast").removeClass().addClass("toast text-bg-warning border-0");
        }
        $('#toastMessage').html(message);
        $('.toast').toast('show');
}



</script>
