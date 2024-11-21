@extends('layouts.app')

@section('title', 'New Shipment')

@section('content')

    <!-- Page Header and Breadcrumb -->
    <div class="d-flex align-items-center mb-4">
        <div class="p-2 flex-fill">
            <h1 class="page-header mb-0">New Shipment</h1>
        </div>
        <div class="ms-auto p-2">
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home', ['organisation' => $organisation]) }}">SMTT</a></li>
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Shipment</a></li>
                <li class="breadcrumb-item active">New Shipment</li>
            </ul>
        </div>
    </div>

    <!-- Form Section -->
    <div class="row gx-4">
        <div class="col-lg-12"> 
            <!-- Form Card -->
            <div class="card">
                <div class="card-body">
                    <form id="createHeaderForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row gx-4">
                            <!-- Receipt Card -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center">
                                        <i class="bi bi-send me-2"></i>
                                        <h5 class="card-title mb-0">Shipment</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Receipt Input -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Shipment No:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="shipment_no" readonly>
                                            </div>
                                        </div>
                                        <!-- Warehouse Input -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Warehouse:</label>
                                            <div class="col-sm-8">
                                                <select class="form-select" name="warehouse" >
                                                    <option value="" selected disabled>Select</option> 
                                                    @foreach($warehouses as $warehouse)
                                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Bill To:</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" name="bill_to" rows="3" readonly>SMT TECHNOLOGIES SDN BHD</textarea>
                                            </div>
                                        </div>
                                        <!-- Ship To -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Ship To:</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control " name="ship_to" rows="4" readonly>
SUPERIOR LOGISTICS (HONG KONG)
KWU TUNG ROAD 105A DD98 LOT 6 
SHEUNG SHUI 999077 NEW 
TERRITORIES HONG KONG
                                                </textarea>
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
                                            <label class="col-sm-4 col-form-label">Customs Slip:</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="customs">
                                                    <label class="input-group-text" for="customs_file">
                                                        <i class="bi bi-upload"></i>
                                                    </label>
                                                    <input type="file" class="form-control d-none" name="customs_file" id="customs_file" onchange="displayFileName('customs_file')">
                                                    <span class="form-text ms-2" id="customs_file_name"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Shipment Slip:</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="shipment_slip">
                                                    <label class="input-group-text" for="shipment_slip_file">
                                                        <i class="bi bi-upload"></i>
                                                    </label>
                                                    <input type="file" class="form-control d-none" name="shipment_slip_file" id="shipment_slip_file" onchange="displayFileName('shipment_slip_file')">
                                                    <span class="form-text ms-2" id="shipment_slip_file_name"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Invoice:</label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="invoice">
                                                    <label class="input-group-text" for="invoice_file">
                                                        <i class="bi bi-upload"></i>
                                                    </label>
                                                    <input type="file" class="form-control d-none" name="invoice_file" id="invoice_file" onchange="displayFileName('invoice_file')">
                                                    <span class="form-text ms-2" id="invoice_file_name"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Shipment Date:</label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control" name="shipment_date"  value="{{ old('shipment_date', date('Y-m-d')) }}">
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
            <div class="card d-none" id="linesCard">
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
                                    <th>Serial Number</th>
                                    <th>Item Code</th>
                                    <th>MPN</th>
                                    <th>Location</th>
                                    <th>Lot</th>
                                    <th>Manufacture Date</th>
                                    <th>Quantity</th>
                                    <th>UOM</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="10">No line items available.</td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                    <!-- Submit Content Form Button -->
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary" id="submit-content-form" disabled>Ship</button>
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
        $(document).ready(function() {

            // Handle Header Form Submission
            $('#createHeaderForm').on('submit', function(event){

                event.preventDefault(); // Prevent default form submission

                let formData = new FormData(this);

                $.ajax({
                    method: 'POST',
                    url: '{{ route('wms.ship.ctrls.store', ['organisation' => $organisation->id]) }}',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response){
                        if(response.status === 'success'){
                            
                            toast(2, 'Shipment created successfully.');

                            // Show the "Lines" card
                            $('#linesCard').removeClass('d-none');

                            window.headerId = response.data.id;

                            $('#createHeaderForm :input').prop('disabled', true);

                            // Scroll down to the "Lines" section
                            $('html, body').animate({
                                scrollTop: $("#linesCard").offset().top
                            }, 1000);

                            $('input[name="shipment_no"]').val(response.data.shipment_no);

                        }
                        else{
                            toast(1, 'Failed to create Shipment.');
                        }
                    },
                    error: function(xhr){
                        // Handle Validation Errors
                        if(xhr.status === 422){
                            let errors = xhr.responseJSON.errors;
                            let errorMessages = '';

                            $.each(errors, function(key, messages){
                                $.each(messages, function(index, message){
                                    errorMessages += message + '<br>';
                                });
                            });

                            toast(1, errorMessages);
                        }
                        else{
                            // General Error
                            toast(1, 'An unexpected error occurred.');
                        }
                    }
                });
            });
        });

         // Define the base URL with a placeholder for header ID
         var createLineUrl = "{{ route('wms.ship.ctrls.contents.create', ['organisation' => $organisation->id, 'header' => 'HEADER_ID_PLACEHOLDER']) }}";

        // Function to open the Add Line Modal
        function openAddModal() {
            $('#modal').modal('show');

            var url = createLineUrl.replace('HEADER_ID_PLACEHOLDER', headerId);

            modal(url, 2);
        }

        // Function to display selected file name
        function displayFileName(inputId) {
            var input = document.getElementById(inputId);
            var fileName = input.files.length > 0 ? input.files[0].name : '';
            document.getElementById(inputId + '_name').textContent = fileName;
        }

        // Function to add a new row to the table
        function addRow(line) {
            // If "No line items available." row exists, remove it
            if($('#myTable tbody tr').length === 1 && $('#myTable tbody tr td').attr('colspan') === '10'){
                $('#myTable tbody').empty();
            }

            let rowCount = $('#myTable tbody tr').length;
            let newRow = `
                <tr id="row-${line.id}">
                    <td>${rowCount + 1}</td>
                    <td>${line.serial_number}</td>
                    <td>${line.item_code}</td>
                    <td>${line.mpn}</td>
                    <td>${line.location}</td>
                    <td>${line.lot}</td>
                    <td>${line.manufacture_date}</td>
                    <td>${line.quantity}</td>
                    <td>${line.uom}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="btnRemove(${line.id})">Delete</button>
                    </td>
                </tr>
            `;
            $('#myTable tbody').append(newRow);
            $('#submit-content-form').prop('disabled', false);
        }

        // Function to remove a row from the table
        function removeRow(id) {
            $(`#row-${id}`).remove();
            updateRowNumbers();

            // If no rows left, show the empty message and disable the receive button
            if($('#myTable tbody tr').length === 0){
                $('#myTable tbody').append(`
                    <tr>
                        <td colspan="10">No line items available.</td>
                    </tr>
                `);
                $('#submit-content-form').prop('disabled', true);
            }
        }

        // Function to update row numbers after removal
        function updateRowNumbers() {
            $('#myTable tbody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
            });
        }

        // Function to remove a line item
        function btnRemove(id) {
            event.preventDefault();
            $.ajax({
                type: "POST",
                url: '{{route('wms.ship.ctrls.contents.destroy', ['organisation' => $organisation->id])}}',
                dataType: 'json',
                data: { 
                    id: id,
                    _token: '{{ csrf_token() }}' // Include the CSRF token
                },
                success: function (d) {
                    if (d.status === 'success' || d.status === 2) 
                    { 
                        toast(2, d.message);
                        removeRow(id);
                    } else {
                        toast(1, d.message);
                    }
                },
                error: function (xhr, status, error) 
                {
                    toast(1, 'Error: ' + error);
                }
            });
        }

        // Handle Receive Button Click
        $('#submit-content-form').on('click', function(){
            toast(2, "Shipment Successfully");
            setTimeout(function(){
                window.location.href = "{{ url()->previous() }}" , 2000
            });
        });
    </script>
@endsection