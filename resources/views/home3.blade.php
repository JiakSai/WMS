<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <title>SMTT | Warehouse management system</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="icon" type="image/png" href="{{ asset('img/company.png') }}">
    <link href="{{ asset('css/vendor.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugins/jvectormap-next/jquery-jvectormap.css') }}" rel="stylesheet" />

</head>

<style>
</style>

<body>

    <div id="app" class="app app-sidebar-collapsed">

        <x-topbar :warehouseName="$warehouse->name" />
        <x-slidebar />

        <button class="app-sidebar-mobile-backdrop" data-toggle-target=".app"
            data-toggle-class="app-sidebar-mobile-toggled"></button>
        <div id="content" class="app-content">
            <div class="row" id="todayWO">
                <div class="col-lg-12">
                    <div id="statsWidget" class="mb-5">
                        <h4>Warehouse Management System</h4>
                        <p>To manage raw material warehouse</p>

                        @foreach ($mainModules as $mainModule)
                            <div class="card mb-5">

                                <div class="card-body">
                                    <div class="text-inverse text-opacity-50 small mb-2"><b>{{ $mainModule->name }}</b>
                                    </div>
                                    <div class="row mb-3">

                                        @foreach ($subModules as $subModule)
                                            @if ($subModule->group == $mainModule->id)
                                                <div class="col-xl-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                                                    <div class="list-group mb-3">
                                                        <a href="{{ route('selectModule', ['warehouse' => $warehouse->id, 'module' => $subModule->id]) }}"
                                                            class="list-group-item list-group-item-action d-flex align-items-center text-inverse">
                                                            <div class="w-40px h-40px d-flex align-items-center justify-content-center text-white rounded-2 ms-n1">
                                                                <i class="{{ $subModule->icon }} fa-lg"></i>
                                                            </div>
                                                                <div class="flex-fill px-3">
                                                                    <div class="fw-bold">{{ $subModule->name }}</div>
                                                                    <div class="small text-inverse text-opacity-50">{{ $subModule->description }}</div>
                                                                </div>
                                                               
                                                
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="card-arrow">
                                    <div class="card-arrow-top-left"></div>
                                    <div class="card-arrow-top-right"></div>
                                    <div class="card-arrow-bottom-left"></div>
                                    <div class="card-arrow-bottom-right"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


            <!-- Modal -->
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
                <div id="liveToast" class="toast text-bg-success border-0" role="alert" aria-live="assertive"
                    aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body" id="toastMessage">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <x-rocket_loader />
    <a href="#" data-toggle="scroll-to-top" class="btn-scroll-top fade"><i class="fa fa-arrow-up"></i></a>

    <script src="{{ asset('js/vendor.min.js') }}"></script>
    <script src="{{ asset('js/app.min.js') }}"></script>
    <script src="{{ asset('js/jquery-3.6.3.min.js') }}"></script>
    <!-- <script src="../../assets/js/JsBarcode.all.js"></script> -->
    <script src="{{ asset('js/qrcode.js') }}"></script>
    <script src="{{ asset('js/moment.min.js') }}"></script>
    {{-- <script src="{{asset('js/index.js')}}"></script> --}}

</body>

</html>

<script></script>
