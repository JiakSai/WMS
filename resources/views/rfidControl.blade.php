<!DOCTYPE html>
<html lang="en" data-bs-theme="dark" class="bg-cover-1">

<head>
    <meta charset="utf-8" />
    <title>SMTT | RFID Control</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="icon" type="image/png" href="{{asset('img/company.png')}}">
    <link href="{{asset('css/vendor.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/app.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/multiSelect.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
    <link href="{{asset('plugins/bootstrap-daterangepicker/daterangepicker.css')}}" rel="stylesheet" />
</head>

<body>

<div id="app" class="app app-sidebar-collapsed">
        <x-topbar :warehouseName="$warehouse->name" />
        <x-slidebar />

        <button class="app-sidebar-mobile-backdrop" data-toggle-target=".app"
            data-toggle-class="app-sidebar-mobile-toggled"></button>
        <div id="content" class="app-content">
            <div class="d-flex align-items-center mb-3">
                <div class="flex-fill">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">SMTT</a></li>
                        <li class="breadcrumb-item">RFID Control</li>
                    </ul>
                    <h1 class="page-header mb-0">RFID Control</h1>
                </div>
                <div class="ms-auto">
                    <a href="#" class="btn btn-outline-theme" data-bs-toggle="modal" data-bs-target="#modal"
                    onclick="modal('{{ route('printRfid', ['warehouse' => $warehouse->id]) }}', 2)"><i class="fa fa-plus-circle me-1"></i> Print RFID</a>
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
                                        <input type="text" class="form-control" name="rfid"
                                            placeholder="RFID" autofocus/>
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
                                <a class="btn btn-outline-theme btn-sm toggle-vis" data-column="1" style="border-radius: 4px;">From</a> - 
                                <a class="btn btn-outline-theme btn-sm toggle-vis" data-column="2" style="border-radius: 4px;">To</a> - 
                                <a class="btn btn-outline-theme btn-sm toggle-vis" data-column="3" style="border-radius: 4px;">Created By</a> -
                                <a class="btn btn-outline-theme btn-sm toggle-vis" data-column="4" style="border-radius: 4px;">Created Time</a> 
                            </div>

                            <table id="myDataTable" class="table table-responsive w-100">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Created By</th>
                                        <th>Created Time</th>
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
            '<table class="table table-bordered w-75 m-auto child-table text-center" id="child-table-' + d.from + '">' +
            '<thead>' +
            '<tr>' +
            '<th class="text-center">RFID</th>' +
            '<th class="text-center">Created By</th>' +
            '<th class="text-center">Created Time</th>' +
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
                url: '{{ route('getRfidData', ['warehouse' => $warehouse->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.rfid = datatable_data.rfid;
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
            { 'data': 'from' },
            { 'data': 'to' },
            { 'data': 'created_by' },
            { 'data' : 'created_at'},
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

            // Initialize child DataTable
            $('#child-table-' + row.data().from).DataTable({
                'processing': true,
                'serverSide': true,
                'searching': false,
                'ordering': false,
                'paging':false,
                "bInfo" : false,
                'ajax': {
                    'url': '{{route('getRfidChildData', ['warehouse' => $warehouse->id])}}',
                    'type': 'POST',
                    'data': {
                        '_token': '{{ csrf_token() }}',
                        'parentId': row.data().from
                    }
                },
                'columns': [
                    { 'data': 'name' },
                    { 'data': 'created_by' },
                    { 'data': 'created_at' },
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

$("#searchForm").submit(function(event) {
    
    event.preventDefault();
    
    // Serialize the form data
    let searchFormData = $(this).serialize();

    datatable_data.rfid = $(this).find('[name="rfid"]').val();
  
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

// $('#modal').on('shown.bs.modal', function () {
//     $(this).hide().show(0);
//     // Reinitialize MultiSelect
//     document.querySelectorAll('[data-multi-select]').forEach(select => new MultiSelect(select));
// });

// $('#modal').on('hidden.bs.modal', function () {
//     $(this).find('.modal-content').html('');
// });

</script>
