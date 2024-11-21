@extends('layouts.app')

@section('title', ' Invoice')

@section('left_title', 'TMSMT')

@section('content')
    <div class="d-flex align-items-center mb-3">
        <div class="p-2 flex-fill"> <h1 class="page-header mb-0">Invoice</h1></div>
        <div class="ms-auto p-2 ">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home', ['organisation' => $organisation])}}">SMTT</a></li>
                <li class="breadcrumb-item">Sales Invoice</li>
            </ul>
        </div>
    </div> 
    <div class="row gx-4">
        <div class="col-lg-12">
            <div class="card">
        
                <div class="card-body">
                    <ul class="nav nav-tabs mb-2 d-flex pt-2" id="myTab" role="tablist">
                        @foreach ($tabs as $index => $tab)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="{{ $tab->code }}-tab" data-bs-toggle="tab" data-route="{{ route($tab->route, ['organisation' => $organisation->id]) }}" data-bs-target="#{{ $tab->code }}" type="button" role="tab" aria-controls="{{ $tab->code }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">{{ $tab->name }}</button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        @foreach ($tabs as $index => $tab)
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="{{ $tab->code }}" role="tabpanel" aria-labelledby="{{ $tab->code }}-tab">
                                <!-- Content will be loaded dynamically via AJAX -->
                           
                            </div>
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
        </div>
    </div>
@endsection

@section('scripts')
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
            function loadTabContent(tabId, route) {
                $.ajax({
                    url: route,
                    type: 'GET',
                    success: function(d) {
                        $('#' + tabId).html(d);
                    },
                    error: function(xhr, status, error) {
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

            // Load initial content
            var initialTab = $('#myTab button.active');
            loadTabContent(initialTab.attr('aria-controls'), initialTab.data('route'));

            // Handle tab change
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("aria-controls"); 
                var route = $(e.target).data('route');
                loadTabContent(target, route);
            });
        });
    </script>
@endsection