<!DOCTYPE html>
<html lang="en" data-bs-theme="dark" class="bg-cover-1">
<head>
    <meta charset="utf-8" />
    <title>SMTT | User Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="icon" type="image/png" href="{{asset('img/company.png')}}">
    <link href="{{asset('css/vendor.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/app.min.css')}}" rel="stylesheet" />
    
    <link href="{{asset('css/dataTables.bootstrap5.min.css')}}" rel="stylesheet"> 
    <link href="{{asset('css/responsive.bootstrap5.min.css')}}" rel="stylesheet"> 
    <link href="{{asset('css/fixedColumns.bootstrap5.min.css')}}" rel="stylesheet"> 
    <link href="{{asset('css/buttons.bootstrap5.min.css')}}" rel="stylesheet"> 

</head>
<body>
    <div id="app" class="app app-sidebar-collapsed">
        <x-topbar :warehouseName="$warehouse->name" />
        <x-slidebar />

        <button class="app-sidebar-mobile-backdrop" data-toggle-target=".app"
            data-toggle-class="app-sidebar-mobile-toggled"></button>
        <div id="content" class="app-content">
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 flex-fill"> <h1 class="page-header mb-0">User Management</h1></div>
                <div class="ms-auto p-2 ">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">SMTT</a></li>
                        <li class="breadcrumb-item">User Management</li>
                    </ul>
                   
                </div>
            </div>
            <div class="row gx-4">
                <div class="col-lg-12">
                    <div class="card">
                  
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-2 d-flex pt-2" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab" aria-controls="user" aria-selected="true">User Control</button>
                                </li>
                                <li class="nav-item me-auto" role="presentation">
                                    <button class="nav-link" id="group-tab" data-bs-toggle="tab" data-bs-target="#group" type="button" role="tab" aria-controls="group" aria-selected="false">Group Control</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" disabled id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Settings</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="user" role="tabpanel" aria-labelledby="user-tab">
                                    <!-- User Control content here -->
                                    <!-- Include the content from the first HTML file -->
                                </div>
                                <div class="tab-pane fade" id="group" role="tabpanel" aria-labelledby="group-tab">
                                    <!-- Group Control content here -->
                                    <!-- Include the content from the second HTML file -->
                                </div>
                                <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="group-tab">
                                    <!-- Settings content here -->
                                    <!-- Include the content from the third HTML file -->
                                </div>
                            </div>
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
                            <div class="spinner-border" style="width: 5rem; height: 5rem;" role="status" >
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
       
      
      
        <script src="{{asset('js/vendor.min.js')}}"></script>
        <script src="{{asset('js/app.min.js')}}"></script>

        <script src="{{asset('js/dataTables.min.js')}}"></script>
        <script src="{{asset('js/dataTables.bootstrap5.min.js')}}"></script>
        <script src="{{asset('js/dataTables.buttons.min.js')}}"></script>

        <script src="{{asset('js/jszip.min.js')}}"></script>
        <script src="{{asset('js/pdfmake.min.js')}}"></script>
        <script src="{{asset('js/vfs_fonts.js')}}"></script>
        <script src="{{asset('js/buttons.colVis.min.js')}}"></script>
        <script src="{{asset('js/buttons.html5.min.js')}}"></script>
        <script src="{{asset('js/buttons.print.min.js')}}"></script>
        <script src="{{asset('js/buttons.bootstrap5.min.js')}}"></script>

        <script src="{{asset('js/dataTables.responsive.min.js')}}"></script>
        <script src="{{asset('js/responsive.bootstrap5.min.js')}}"></script>
        <script src="{{asset('js/dataTables.fixedColumns.min.js')}}"></script>
        <script src="{{asset('js/fixedColumns.bootstrap5.min.js')}}"></script>

{{-- 
        <script src="{{asset('plugins/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-buttons/js/jszip.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-buttons/js/pdfmake.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-buttons/js/vfs_fonts.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-buttons/js/buttons.colVis.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-buttons/js/buttons.print.min.js')}}"></script>

        <script src="{{asset('plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
        <script src="{{asset('plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
        <script src="{{asset('plugins/fixedcolumns/js/dataTables.fixedColumns.js')}}"></script>
        <script src="{{asset('plugins/fixedcolumns/js/fixedColumns.bootstrap5.js')}}"></script> --}}

  
    </body>
</html>

<script>
    $(document).ready(function() {
        // Initialize tabs
        var tabs = new bootstrap.Tab(document.querySelector('#myTab button[data-bs-toggle="tab"]'));
        
        // Handle tab switching
        $('#myTab button').on('click', function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
    
        // Load content for active tab
        function loadTabContent(tabId) {
            var url;
            if (tabId === 'user') {
                url = '{{ route("userControl", ["warehouse" => $warehouse->id]) }}';
            } else if (tabId === 'group') {
                url = '{{ route("groupControl", ["warehouse" => $warehouse->id]) }}';
            }else{
                url = '';
            }
    
            $.ajax({
                url: url,
                type: 'GET',
                success: function(d) {
                    $('#' + tabId).html(d);
                },
                error: function(xhr, status, error) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('loginPage') }}'; 
                        alert('Your session has expired. Please log in again.');
                    } else {
                        alert('Error: ' + error.message);
                        $("#modal").modal('hide');
                    }
                }
            });
        }
    
        // Load initial content
        loadTabContent('user');
    
        // Handle tab change
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("aria-controls");
            loadTabContent(target);
        });
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