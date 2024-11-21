@extends('layouts.app')

@section('title', 'MRB Level Conrol')

@section('content')
    <style>
        #searchDiv {
            position: absolute; /* Absolute, so it's positioned based on the input field */
            background-color: var(--bs-app-header-bg);
            border: 1px solid;
            border-radius: 10px;
            max-height: 300px;
            overflow-y: auto;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 10px;
            display: none;
        }
    </style>
    <div class="d-flex align-items-center mb-3">
        <div class="p-2 flex-fill"> <h1 class="page-header mb-0">@if($mrbheader == []) Create New @else View @endif E-MRB</h1></div>
        <div class="ms-auto p-2 ">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('home', ['organisation' => $organisation])}}">SMTT</a></li>
                <li class="breadcrumb-item">@if($mrbheader == [])New @else View @endif E-MRB</li>
            </ul>
        </div>
    </div>

    
    <div class="row gx-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="nav-wizards-container">
                        <nav class="nav nav-wizards-3 mb-2">
                            <!-- Default Level 0 with description 'Initiator' -->
                            <div class="nav-item col">
                                <a class="nav-link {{ isset($mrbLevel[0]) && $mrbLevel[0] ? 'active' : 'disabled' }}">
                                    <div class="nav-dot mb-2"></div>
                                    <div class="nav-no text-center fw-bold">Level 0</div>
                                    <div class="nav-text">Initiator</div>
                                </a>
                            </div>
                    
                            <!-- Loop through the other levels -->
                            @foreach ($tabs as $index => $tab)
                            <div class="nav-item col ">
                                <a class="nav-link 
                                    {{ $index + 1 === $currentStep ? 'active' : '' }}
                                    {{ $index + 1 < $currentStep ? 'completed' : '' }}
                                    {{ $index + 1 > $currentStep ? 'disabled' : '' }}">
                                    <div class="nav-dot mb-2 @if (isset($mrbLevel[$index+1])){{ $mrbLevel[$index+1]->status == 'void' ? 'border-danger' : '' }}@endif"></div>
                                    <div class="nav-no text-center fw-bold @if (isset($mrbLevel[$index+1])){{ $mrbLevel[$index+1]->status == 'void' ? 'text-danger' : '' }}@endif">Level {{ $index + 1 }}</div>
                                    <div class="nav-text">
                                          
                                    @if (isset($mrbLevel[$index+1]))
                                        {{ $mrbLevel[$index+1]->created_by }}
                                        <br>
                                        <span class="{{ $mrbLevel[$index + 1]->status == 'void' ? 'text-danger' : '' }}">
                                            {{ $mrbLevel[$index + 1]->created_at }}
                                        </span>
                                        <br>
                                        <span class="fw-bold {{ $mrbLevel[$index + 1]->status == 'void' ? 'text-danger' : '' }}">
                                            {{ $mrbLevel[$index+1]->status == 'void' ? 'Void' : 'Approved' }}
                                        </span>
                                    @else
                                        {{ $tab->descriptions }}
                                    @endif
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </nav>
                    </div>
                    
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>          
            <div class="card">
                <div class="card-body">
                    <form id="createEmrbForm" method="POST" >
                        @csrf
                        <div class="row">
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="cost_born_by" class="col-form-label">Cost Born By:</label>
                            </div>
                            <div class="col-lg-2 col-6 mb-3">
                                @if($mrbheader != [])
                                <input type="text" id="cost_born_by" name="cost_born_by" class="form-control border-0" value="{{ $mrbheader->cost_born_by ?? '' }}" readOnly>
                                @else
                                <select id="cost_born_by" name="cost_born_by" class="form-select">
                                    <option value="internal">Internal</option>
                                    <option value="external_supplier">External - Supplier</option>
                                    <option value="external_customer">External - Customer</option>
                                </select>
                                @endif
                            </div>
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="infor_ln_transfer_no" class="col-form-label">Transfer No:</label>
                            </div>
                            <div class="col-lg-2 col-6 mb-3">
                                <input type="text" id="transfer_no" name="transfer_no" class="form-control border-0" readOnly>
                            </div>
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="mrb_number" class="col-form-label">MRB Number:</label>
                                
                            </div>
                            <div class="col-lg-2 col-6 mb-3">
                                <input type="text" id="mrb_number" name="mrb_number" class="form-control border-0" value="{{ $mrbheader->mbr_number ?? '' }}" readOnly>
                            </div>
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="date_submit" class="col-form-label">Date Submit:</label>
                            </div>
                            <div class="col-lg-2 col-6">
                                {{ $mrbheader->date_submit ?? '' }}
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="mes_kitlist" class="col-form-label">MES KitList:</label>
                            </div>
                            <div class="col-lg-2 col-6">
                                @if($mrbheader != [])
                                    <div class="input-group" id="find-mes-kitlist">
                                        <input type="text" class="form-control border-0" required name="mes_kitlist" style="text-transform: uppercase" placeholder="XXXXX" id="mes-wo-input" value="{{$mrbheader->mes_kitlist }}">
                                    </div>
                                @else
                                    <!-- If mes_wo exists, show the value -->
                                    <div class="input-group d-none" id="mes-wo">
                                        <input type="text" class="form-control" required name="mes_wo" style="text-transform: uppercase" placeholder="XXXXX" id="mes-wo-input" value="" readonly>
                                        <button type="button" class="btn btn-outline-theme" onClick="reset_input()" id="sync">Reset</button>
                                    </div>                                
                                    <!-- If mes_wo does not exist, show mes_kitlist input group -->
                                    <div class="input-group" id="find-mes-kitlist">
                                        <input type="text" class="form-control" required name="mes_kitlist" style="text-transform: uppercase" placeholder="XXXXX" id="find-mes-kitlist-input">
                                        <button type="button" class="btn btn-outline-theme" onClick="inforln_check()" id="sync">Sync <i class="bi bi-arrow-repeat"></i></button>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </div>
                                @endif

                                <div id="searchDiv">
                                    <span class="spinner-border spinner-border-sm d-none spinner-border-search" role="status" aria-hidden="true"></span>
                                    <div id="searchResults">
                                        <span class="spinner-border spinner-border-sm d-none spinner-border-search" role="status" aria-hidden="true"></span>
                                    </div>    
                                </div>

                            </div>
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="plant" class="col-form-label">Plant:</label>
                            </div>
                            <div class="col-lg-2 col-6">
                                <input type="text" id="plant" name="plant" class="form-control border-0" value="{{ $mrbheader->plant ?? '' }}" readOnly>
                            </div>
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="line" class="col-form-label">Line:</label>
                            </div>
                            <div class="col-lg-2 col-6">
                                <input type="text" id="line" name="line" class="form-control border-0" value="{{ $mrbheader->line ?? '' }}" readOnly>
                            </div>
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="model" class="col-form-label">Model:</label>
                            </div>
                            <div class="col-lg-2 col-6">
                                <input type="text" id="model" name="model" class="form-control border-0" value="{{ $mrbheader->model ?? '' }}" readOnly>
                            </div>
                        </div>
                        <!-- Type and Customer -->
                        <div class="row">
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="type" class="col-form-label">Type:</label>
                            </div>
                            <div class="col-lg-2 col-6">
                                @if($mrbheader != [])
                                <input type="text" id="type" name="type" class="form-control border-0" value="{{ $mrbheader->type ?? '' }}" readOnly>
                                @else
                                <select id="type" name="type" class="form-select">
                                    <option value="kitlist">By KitList</option>
                                    <option value="other">Other</option>
                                </select>
                                @endif
                            </div>
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="customer" class="col-form-label">Organisation:</label>
                            </div>
                            <div class="col-lg-2 col-6">
                                <input type="text" id="customer" name="customer" class="form-control border-0" value="{{$mrbheader->organisation_name ?? $organisation->name}}" readonly>
                            </div>
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="initiator" class="col-form-label" >Initiator:</label>
                            </div>
                            <div class="col-lg-2 col-6">
                                <input type="text" id="initiator" name="initiator" class="form-control border-0" placeholder="Initiator" value="{{$mrbheader->initiator ?? Auth::user()->name }}" readonly>
                            </div> 
                            <div class="col-lg-1 col-6 d-flex align-items-center mb-3">
                                <label for="department" class="col-form-label">Department:</label>
                            </div>
                            <div class="col-lg-2 col-6">
                                <input type="text" id="department" name="department" class="form-control border-0" placeholder="Department" value="{{$mrbheader->department ?? App\Models\Sys\Usrm\SysUsrmGrpcs::find(Auth::user()->group)->name }}" readonly>
                            </div>                                                       
                        </div>
                        @if($mrbheader == [])
                        <!-- Submit Buttons -->
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                        @endif
                    </form>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center p-2">
                        <h1 class="page-header mb-0">E-MRB Content</h1>
                        @if($mrbheader != [] && $currentStep == -1)
                        <button class="btn btn-outline-theme" tabindex="0" aria-controls="mainDataTable" type="button" onclick="openAddModal()">
                            <span><i class="fa fa-plus-circle me-1"></i> Add</span>
                        </button>
                        @endif
                    </div>
                    <form id="emrb-content" method="POST">
                        <table id="myTable" class="table table-bordered text-center order-list">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Part No.</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Currency</th>
                                    <th>Unit Price</th>
                                    <th>Amount</th>
                                    <th>Defect</th>
                                    <th>Location</th>
                                    <th>Root Cause</th>
                                    <th>Correction</th>
                                    <th>Disposition</th>
                                    <th>Remark</th>
                                    <th>File</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                    <!-- Submit Buttons -->
                    @if ($mrbheader != [] && $currentStep == -1)
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="submit-mrb-form" disabled>Submit Mrb Form</button>
                    </div>
                    @endif
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
            @if ($currentStep != -1 && $mrbLevel[$currentStep]->status != 'void')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <h5>{{ $tabs[$currentStep]->descriptions }} (Next Approval)</h5>
                        <div class="col-lg-2 col-6 d-flex align-items-center mb-3">
                            <label for="approver_name" class="col-form-label">Approver Name:</label>
                        </div>
                        <div class="col-lg-5 col-6 ">
                            @if (!$is_user)
                                <label for="approver_name" class="col-form-label">
                                    @php
                                        $count = 0;
                                        $login_username = Auth::user()->username;

                                    @endphp
                                    
                                    @foreach ($approver_list as $index_approver => $approve)
                                        @if ($count > 0)
                                            /
                                        @endif
                                            {{$approve->user_name}}
                                        
                                        @php
                                            $count++;
                                        @endphp
                                    @endforeach
                                </label>
                            @else
                                <label for="approver_name" class="col-form-label">
                                    {{Auth::user()->name}}
                                </label>
                            @endif
                        </div>
                        @if ($is_user)
                            <div class="row">
                                
                                <div class="col-lg-2 col-4 d-flex align-items-center mb-3">
                                    <label for="remark" class="col-form-label">Remark:</label>
                                </div>

                                <div class="col-lg-10 col-8">
                                    <textarea id="remark" name="remark" class="form-control"></textarea>
                                </div>
                                @if ( $mrbLevel[$currentStep]->status != 'void')
                                <div class="modal-footer my-3">
                                    <button type="submit" class="btn btn-danger me-3 void-step">Void</button>
                                    <button type="submit" class="btn btn-primary approve-step">Approve</button>
                                </div> 
                                @endif                               
                            </div>

                        @endif
                    </div>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
            @endif  
        </div>
    </div>
    
@endsection

@section('scripts')
    <script>
        var currentStep = @json($currentStep); 
        loadTableData();

        function reset_input(){
            $('#mes-wo').addClass('d-none');
            $('#find-mes-kitlist').removeClass('d-none');
            // Populate the selected employee ID and full name in the respective input fields
            $('#find-mes-kitlist-input').val('');

            $('#model').val('');
            $('#plant').val('');
            $('#line').val('');
        }

       function inforln_check() {
            $("#sync").prop("disabled", true).find("span.spinner-border").removeClass("d-none");
            let spinner = $('#searchDiv').find('.spinner-border-search');
            spinner.removeClass('d-none'); // Show spinner
            $('#searchResults').empty(); // Clear previous results (if needed)

            $.ajax({
                type: "POST",
                url: '{{ route('mrb.emrb.loadWoDetails', ['organisation' => $organisation->id]) }}',
                dataType: 'json',
                data: {
                    wo: $('[name="mes_kitlist"]').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Since the response is an array, we access the first element
                    const data = response[0];
                    $('#searchDiv').css({
                        width: $('#searchInput').outerWidth() // same width as the input field
                    });
                    spinner.addClass('d-none'); 
                    if (data.Success === true) {
                        $("#submit").prop("disabled", false);
                        // Access the WorkOrderDatas if Success is true
                        if (data.Result && data.Result.WorkOrderDatas) {
                            data.Result.WorkOrderDatas.forEach(function(workOrder) {
                                $('#searchResults').append(`<a href="#" data-plant="${workOrder.Plant}" data-line="${workOrder.Line}" data-model="${workOrder.Model}">${workOrder.MesWorkOrder}</a></br>`);
                            });
                        }
                        $('#searchDiv').fadeIn();
                    } else {
                        // Show error message if Success is false
                        toast(1, data.Message || 'An error occurred.');
                        $('#searchDiv').fadeOut();
                    }
                    
                    $("#sync").prop("disabled", false).find("span.spinner-border").addClass("d-none");
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                    $("#sync").prop("disabled", false).find("span.spinner-border").addClass("d-none");
                }
            });
        }
        function openAddModal() {
            var wo = $('#mes-wo-input').val();
            var formId = $('#mrb_number').val();
            if (wo != '') {
                $('#modal').modal('show');
                var url = '{{ route('mrb.emrb.create', ['organisation' => $organisation->id, 'customer' => '__wo__', 'mrbFormId' => '__formId__']) }}'.replace('__wo__', wo).replace('__formId__', formId);
                modal(url, 2);
            } else {
                toast(1, "Make Sure Select one MES KitList");
            }
        }
        function loadTableData() {
            let mrbNumber = $('#mrb_number').val();
            var wo = $('#mes-wo-input').val();
            $.ajax({
                url: '{{ route('mrb.emrb.showEmrbContent', ['organisation' => $organisation->id]) }}',
                dataType: 'json',
                method: 'POST',
                data: {
                    mrbNumber: mrbNumber,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    let tableBody = $('#myTable tbody');
                    if(response.data != []){
                        try{
                            document.getElementById('submit-mrb-form').disabled = false;  
                        } catch(e){}
                        
                    } else{
                        document.getElementById('submit-mrb-form').disabled = true;
                    }
                    tableBody.empty(); // Clear existing table rows

                    response.data.forEach(function(item, index) {
                        var url = '{{ route('mrb.emrb.edit', ['organisation' => $organisation->id, 'customer' => '__wo__', 'mrbFormId' => '__formId__']) }}'.replace('__wo__', wo).replace('__formId__', item.id);

                        let row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.part_number}</td>
                                <td>${item.description}</td>
                                <td>${item.quantity}</td>
                                <td>${item.currency}</td>
                                <td>${item.unit_price}</td>
                                <td>${item.amount}</td>
                                <td>${item.defect_name}</td>
                                <td>${item.location}</td>
                                <td>${item.root_cause}</td>
                                <td>${item.correction}</td>
                                <td>${item.disposition_name}</td>
                                <td>${item.remark}</td>
                                <td>${item.file_path ? `<a href="http://168.168.1.30:8888/storage/images/${item.file_path}" target="_blank">Show In New Tab</a>` : 'No file'}</td>
                                <td>
                            `    
                            if (currentStep == -1) {
                                row += 
                                `
                                    <center>
                                        <div class="dropdown">   
                                            <button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">  
                                                <i class="bi bi-list me-1"></i> Menu   
                                            </button> 
                                            <ul class="dropdown-menu z-1">
                                                <li>
                                                    <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal('${url}', '${item.id}')">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </a>
                                                </li>
                                                <li><a href="#" class="dropdown-item" onClick="btnRemove('${item.id}')"><i class="bi bi-trash-fill"></i> Delete</a></li>
                                            </ul>  
                                        </div>
                                    </center>                                
                                `
                            }
                        row +=   `</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load data:', error);
                }
            });
        }
        function btnRemove(id) {
            $.ajax({
                type: "DELETE",
                url: '{{route('mrb.emrb.destroyContent', ['organisation' => $organisation->id])}}',
                dataType: 'json',
                data: { 
                    id: id,
                    _token: '{{ csrf_token() }}' // Include the CSRF token
                },
                success: function (d) {
                    if (d.status === 2) {
                        toast(2, d.message);
                        loadTableData();
                    } else {
                        toast(1, d.message);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Error: ' + error.message);
                }
            });
        }
        $(document).ready(function() {
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#searchInput, #searchResults').length) {
                $('#searchDiv').fadeOut();
                }
            });

            $('.approve-step').on('click', function(event) {
                event.preventDefault(); // Prevent the default form submission behavior
                let mrbNumber = $('#mrb_number').val();
                let remark = $('#remark').val();
                // Confirm approval action
                if (confirm('Are you sure you want to approve this step?')) {
                    // Perform the approval action (e.g., send an AJAX request)
                    $.ajax({
                        url: '{{ route('mrb.emrb.approveMrb', ['organisation' => $organisation->id]) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            mrb_number: mrbNumber,
                            remark: remark,
                        },
                        success: function (d) {
                            if (d.status === 2) {
                                toast(2, d.message);
                                location.reload();
                            } else {
                                toast(1, d.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred: ' + error);
                        }
                    });
                }
            });

            // Handle the click event for the "Void" button
            $('.void-step').on('click', function(event) {
                event.preventDefault(); // Prevent the default form submission behavior
                let mrbNumber = $('#mrb_number').val();
                let remark = $('#remark').val();
                // Confirm void action
                if (confirm('Are you sure you want to void this step?')) {
                    // Perform the void action (e.g., send an AJAX request)
                    $.ajax({

                        url: '{{ route('mrb.emrb.rejectMrb', ['organisation' => $organisation->id]) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            mrb_number: mrbNumber,
                            remark: remark,
                        },
                        success: function (d) {
                            if (d.status === 2) {
                                toast(2, d.message);
                                location.reload();
                            } else {
                                toast(1, d.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred: ' + error);
                        }
                    });
                }
            });
            $('#submit-mrb-form').click(function() {
                // Get the value of the MRB Number input
                var mrbNumber = $('#mrb_number').val().trim();

                // Confirm with the user before submitting
                if (confirm('Are you sure you want to submit the MRB Form with MRB Number: ' + mrbNumber + '?')) {
                    // Send the value to the server using AJAX
                    $.ajax({
                        url: '{{route('mrb.emrb.submitMrbForm', ['organisation' => $organisation->id])}}',
                        method: 'POST',
                        data: {
                            mrb_number: mrbNumber,
                            _token: '{{ csrf_token() }}' // Include CSRF token for security if using Laravel
                        },
                        success: function(response) {
                            location.refresh();
                        },
                        error: function(xhr, status, error) {
                            alert('Error: ' + error);
                            // Optionally handle error
                        }
                    });
                }
            });
            $(document).on('click', '#searchDiv a', function(e) {
                e.preventDefault(); // Prevent the default anchor behavior

                // Get the selected employee's ID and name from the clicked link
                const model = $(this).data('model');
                const plant = $(this).data('plant');
                const line = $(this).data('line');
                const mes_wo = $(this).text().trim(); // Get mes_wo from the link text

                $('#mes-wo').removeClass('d-none');
                
                $('#find-mes-kitlist').addClass('d-none');
                // Populate the selected employee ID and full name in the respective input fields
                $('#mes-wo-input').val(mes_wo);
                $('#model').val(model);
                $('#plant').val(plant);
                $('#line').val(line);
                // Hide the search results after selection
                $('#searchDiv').fadeOut();
            });
            $('#createEmrbForm').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Gather form data
                let formDataArray = $(this).serializeArray();
                let formData = {};
                formDataArray.forEach(function(item) {
                    if (formData[item.name]) {
                        if (Array.isArray(formData[item.name])) {
                            formData[item.name].push(item.value);
                        } else {
                            formData[item.name] = [formData[item.name], item.value];
                        }
                    } else {
                        formData[item.name] = item.value;
                    }
                });


                formData['_token'] = '{{ csrf_token() }}';

                $.ajax({
                    method: "POST",
                    url: '{{ route('mrb.emrb.store', ['organisation' => $organisation->id]) }}',
                    data: formData,
                    success: function(d) {
                        // Hide the spinner

                        if (d.status === 2) {                         
                            // Redirect to the specified URL
                            window.location.href = "http://168.168.1.30:8888/wms/2/emrb/newEMRB/" + d.message;
                        } else if (d.status === 1) {
                            // If status is 1, show error messages
                            let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                            toast(1, errorMessages);
                        }
                    },
                    error: function(xhr, status, error) {
                        toast(1, "Error:", xhr.status, status, error);
                    }
                });
            });
            // Initialize tabs
            // var tabs = new bootstrap.Tab(document.querySelector('#myTab button[data-bs-toggle="tab"]'));
            
            // // Handle tab switching
            // $('#myTab button').on('click', function (e) {
            //     e.preventDefault();
            //     $(this).tab('show');
            // });
        
            // // Load content for active tab
            // function loadTabContent(tabId, route) {
            //     $.ajax({
            //         url: route,
            //         type: 'GET',
            //         success: function(d) {
            //             $('#' + tabId).html(d);
            //         },
            //         error: function(xhr, status, error) {
            //             if (xhr.status === 401) {
            //                 window.location.href = '{{ route('login.page') }}'; 
            //                 alert('Your session has expired. Please log in again.');
            //             } else {
            //                 alert('Error: ' + error.message);
            //                 $("#modal").modal('hide');
            //             }
            //         }
            //     });
            // }
        
            // // Load initial content
            // var initialTab = $('#myTab button.active');
            // loadTabContent(initialTab.attr('aria-controls'), initialTab.data('route'));
        
            // // Handle tab change
            // $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            //     var target = $(e.target).attr("aria-controls"); 
            //     var route = $(e.target).data('route');
            //     loadTabContent(target, route);
            // });
        });
    </script>
@endsection