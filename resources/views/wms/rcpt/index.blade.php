@extends('layouts.app')

@section('title', 'Warehouse Receipt')

@section('content')

    <!-- Page Header and Breadcrumb -->
    <div class="d-flex align-items-center mb-4">
        <div class="p-2 flex-fill">
            <h1 class="page-header mb-0">Warehouse Receipt</h1>
        </div>
        <div class="ms-auto p-2">
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home', ['organisation' => $organisation]) }}">SMTT</a></li>
                <li class="breadcrumb-item active">Warehouse Receipt</li>
            </ul>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row gx-4">
        <div class="col-lg-12"> 
            <!-- Form Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="createHeaderForm" method="POST">
                        @csrf
                        <div class="row gx-4">
                            <!-- Receipt Card -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>
                                        <h5 class="card-title mb-0">Receipt</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Receipt Input -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Receipt:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="receipt" required>
                                            </div>
                                        </div>
                                        <!-- Warehouse Input -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Warehouse:</label>
                                            <div class="col-sm-8">
                                                <select class="form-select" name="warehouse" required>
                                                    <option value="" selected disabled>Select</option> 
                                                    @foreach($warehouses as $warehouse)
                                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Optional Arrow Decorations -->
                                    <div class="card-arrow">
                                        <div class="card-arrow-top-left"></div>
                                        <div class="card-arrow-top-right"></div>
                                        <div class="card-arrow-bottom-left"></div>
                                        <div class="card-arrow-bottom-right"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Delivery Card -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center">
                                        <i class="bi bi-truck me-2"></i>
                                        <h5 class="card-title mb-0">Delivery</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Packing Slip Input -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Packing Slip:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="packing" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">DO:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="do" required>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Receipt Date:</label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control" name="receipt_date" required value="{{ old('receipt_date', date('Y-m-d')) }}">
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
                        <!-- Save Button -->
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="card">
                <div class="card-body">
                    <!-- Content Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">Lines</h2>
                        <button class="btn btn-outline-secondary" type="button" onclick="openAddModal()">
                            <span><i class="bi bi-plus-circle me-1"></i>Add</span>
                        </button>
                    </div>
                    <!-- Content Table -->
                    <form id="content" method="POST">
                        <table id="myTable" class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>RFID</th>
                                    <th>Item Code</th>
                                    <th>MPN</th>
                                    <th>Lot</th>
                                    <th>Manufacture Date</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dynamic Rows Here -->
                            </tbody>
                        </table>
                    </form>
                    <!-- Submit Mrb Form Button -->
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary" id="submit-mrb-form" disabled>Receive</button>
                    </div>
                </div>
                <!-- Optional Arrow Decorations -->
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
        // loadTableData();

        function openAddModal() {
            $('#modal').modal('show');
            modal('{{ route('wms.rcpt.create', ['organisation' => $organisation->id]) }}', 2);
        }

        // function loadTableData() {
        //     let mrbNumber = $('#mrb_number').val();
        //     var wo = $('#mes-wo-input').val();
        //     $.ajax({
        //         url: '{{ route('mrb.emrb.showEmrbContent', ['organisation' => $organisation->id]) }}',
        //         dataType: 'json',
        //         method: 'POST',
        //         data: {
        //             mrbNumber: mrbNumber,
        //             _token: '{{ csrf_token() }}'
        //         },
        //         success: function(response) {
        //             let tableBody = $('#myTable tbody');

        //             tableBody.empty(); // Clear existing table rows

        //             response.data.forEach(function(item, index) {
        //                 var url = '{{ route('mrb.emrb.edit', ['organisation' => $organisation->id, 'customer' => '__wo__', 'mrbFormId' => '__formId__']) }}'.replace('__wo__', wo).replace('__formId__', item.id);

        //                 let row = `
        //                     <tr>
        //                         <td>${index + 1}</td>
        //                         <td>${item.part_number}</td>
        //                         <td>${item.description}</td>
        //                         <td>${item.quantity}</td>
        //                         <td>${item.currency}</td>
        //                         <td>${item.unit_price}</td>
        //                         <td>${item.amount}</td>
        //                         <td>${item.defect_name}</td>
        //                         <td>${item.location}</td>
        //                         <td>${item.root_cause}</td>
        //                         <td>${item.correction}</td>
        //                         <td>${item.disposition_name}</td>
        //                         <td>${item.remark}</td>
        //                         <td>${item.file_path ? `<a href="${item.file_path}" target="_blank">Download</a>` : 'No file'}</td>
        //                         <td>
        //                             <center>
        //                                 <div class="dropdown">   
        //                                     <button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">  
        //                                         <i class="bi bi-list me-1"></i> Menu   
        //                                     </button> 
        //                                     <ul class="dropdown-menu z-1">
        //                                         <li>
        //                                             <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal('${url}', '${item.id}')">
        //                                                 <i class="bi bi-pencil-square"></i> Edit
        //                                             </a>
        //                                         </li>
        //                                         <li><a href="#" class="dropdown-item" onClick="btnRemove('${item.id}')"><i class="bi bi-trash-fill"></i> Delete</a></li>
        //                                     </ul>  
        //                                 </div>
        //                             </center>
        //                         </td>
        //                     </tr>
        //                 `;
        //                 tableBody.append(row);
        //             });
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Failed to load data:', error);
        //         }
        //     });
        // }

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