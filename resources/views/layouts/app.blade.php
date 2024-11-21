<!DOCTYPE html>
<html lang="en" data-bs-theme="dark" class="bg-cover-1">
<head>
    <meta charset="utf-8" />
    <title> @yield('left_title', 'SMTT') | @yield('title', 'Default Title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="icon" type="image/png" href="{{ asset('img/company.png') }}">
    <link href="{{ asset('css/vendor.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" />
    
    <link href="{{ asset('css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/responsive.bootstrap5.min.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/fixedColumns.bootstrap5.min.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/buttons.bootstrap5.min.css') }}" rel="stylesheet">

    
    @stack('css')
</head>
<body>
    <div id="app" class="app app-sidebar-collapsed">
        <x-topbar :org="$organisation" />
        <x-slidebar :org="$organisation" />
        
        <button class="app-sidebar-mobile-backdrop" data-toggle-target=".app"
        data-toggle-class="app-sidebar-mobile-toggled"></button>

        <div id="content" class="app-content">
            @yield('content')
            {{-- modal-dialog-scrollable --}}
            <div class="modal fade" id="modal" data-bs-backdrop="static">
                <div class="modal-dialog modal-xl">
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
    </div>
   
    <script src="{{ asset('js/vendor.min.js') }}"></script>
    <script src="{{ asset('js/app.min.js') }}"></script>

    <script src="{{ asset('js/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>

    <script src="{{ asset('js/jszip.min.js') }}"></script>
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('js/buttons.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.fixedColumns.min.js') }}"></script>
    <script src="{{ asset('js/fixedColumns.bootstrap5.min.js') }}"></script>
    @stack('scriptsLib')

    @yield('scripts')

    <script>
        function modal(route,id) {
            $('#modalContent').html('<div class="text-center"><div class="spinner-border" style="width: 5rem; height: 5rem;" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            $.ajax({
                method: "POST",
                url: route,
                dataType: 'json',
                data: { 
                    id: id,
                    _token: '{{ csrf_token() }}' // Include the CSRF token
                },
                success: function (d) {
                    $('#modalContent').html(d.data);
                },
                error: function (xhr, status, error) {
                    if (xhr.status === 401) {
                        window.location.href = '{{ route('login.page') }}'; 
                        alert('Your session has expired. Please log in again.');
                    } else {
                        alert('Error: ' + error.message);
                        $("#modal").modal('hide');
                    }
                }
            });
        }

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
</body>
</html>