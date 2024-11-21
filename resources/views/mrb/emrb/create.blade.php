<style>
    .custom-search-div {
        position: absolute; /* Absolute, so it's positioned based on the input field */
        background-color: var(--bs-app-header-bg);
        border: 1px solid;
        border-radius: 10px;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        padding: 10px;
        display: none; /* Initially hidden */
    }
</style>

<link href="{{ asset('css/multiSelect.css') }}" rel="stylesheet">

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Create Kitlist</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="{{ isset($content) ? 'editForm' : 'addForm' }}" method="POST">
    @csrf
    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
    
        <!-- Row for Part Number -->
        <div class="row mb-3">
            <label class="col-1 col-form-label">Part Number</label>
            <div class="col-3">
                <input type="hidden" class="form-control border-0" name="mes_wo" id="mes_wo" value={{$mes_wo}} readonly />

                @if($wo != [])
                    <select id="part_number" name="part_number" class="form-select user-multi-select" multiple></select>
                @else
                    <input type="text" class="form-control border-0" name="part_number" id="part_number" value={{$content->part_number}} readonly />
                @endif
            </div>
            <label class="col-1 col-form-label">Description</label>
            <div class="col-3">
                <input type="text" class="form-control border-0" name="description" id="description"
                @isset($content) value="{{$content->description}}" @endisset readonly />
            </div>
            <label class="col-1 col-form-label">Location</label>
            <div class="col-3">
                <input type="text" class="form-control border-0" name="location" id="location"
                @isset($content) value="{{$content->location}}" @endisset readonly />
            </div>

        </div>
        
        <!-- Row for Currency -->
        <div class="row mb-3">
            <label class="col-1 col-form-label">Quantity</label>
            <div class="col-2">
                <input type="number" class="form-control" name="quantity" id="quantity" 
                @isset($content) max="{{$quantity}}" value="{{$content->quantity}}" @endisset />
            </div>
            <label class="col-1 col-form-label">Unit Price</label>
            <div class="col-2">
                <input type="text" class="form-control border-0" name="unit_price" id="unit_price" 
                @isset($content) value="{{$content->unit_price}}" @endisset readOnly/>
            </div>
            <label class="col-1 col-form-label">Amount</label>
            <div class="col-2">
                <input type="text" class="form-control border-0" name="amount" id="amount"
                @isset($content) value="{{$content->amount}}" @endisset  readonly />
            </div>
            <label class="col-1 col-form-label">Currency</label>
            <div class="col-2">
                <input type="text" class="form-control border-0" name="currency" id="currency"
                @isset($content) value="{{$content->currency}}" @endisset readonly />
            </div>
        </div>
        
        <!-- Row for Amount -->
        <div class="row mb-3">
            <label class="col-2 col-form-label">Defect</label>
            <div class="col-4">
                <select class="form-select" name="defect" id="defect" multiple></select>
            </div>
            <label class="col-2 col-form-label">Disposition</label>
            <div class="col-4">
                <select id="disposition" name="disposition" multiple></select>
            </div>
        </div>
        
        <!-- Row for Location -->
        <div class="row mb-3">
            <label class="col-2 col-form-label">Root Cause</label>
            <div class="col-4">
                <textarea class="form-control" name="root_cause" id="root_cause">@isset($content){{$content->root_cause}}@endisset</textarea>
            </div>
            <label class="col-2 col-form-label">Correction</label>
            <div class="col-4">
                <textarea class="form-control" name="correction" id="correction">@isset($content){{$content->correction}}@endisset</textarea>
            </div>
        </div>
        
        <!-- Row for Remark -->
        <div class="row mb-3">
            <label class="col-2 col-form-label">Remark</label>
            <div class="col-4">
                <textarea class="form-control" name="remark" id="remark">@isset($content){{$content->remark}}@endisset</textarea>
            </div>
            <label class="col-2 col-form-label">File</label>
            <div class="col-4">
                <input type="file" class="form-control" name="file" id="file" />
                @isset($content)
                    @php
                        $filename = $content->file_path;
                        $shortFilename = Str::limit(basename($filename), 30);
                        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
                    @endphp
                    <small class="text-muted">Current File: {{ $shortFilename }}.{{ $fileExtension }}</small>
                @endisset
            </div>
        </div>
    
    </div>
    
    
    <div class="modal-footer d-flex">
        @if($wo != [])
        <button type="submit" class="btn btn-primary" value="{{$mrbFormId }}" id="addButton">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        @else
        <button type="submit" class="btn btn-primary" value="{{$content->id }}" id="editButton">Update
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        @endif
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script src="{{ asset('js/multiSelect.js') }}"></script>
<script>
    $(document).ready(function () {
        var woData = @json($wo);
        var dispositions = @json($dispositions);
        var defects = @json($defects);
        var selected_disposition_id = @json($content->disposition ?? null);
        var selected_defect_id = @json($content->defect ?? null);
        var content_id = @json($content->id ?? null); 
        //Dispositions Multi Select
        const selectElement = document.getElementById('disposition');
        dispositions.forEach(disposition => {
            const option = document.createElement('option');
            option.value = disposition.id;
            option.text = disposition.name;
            if (disposition.id == selected_disposition_id) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });

        new MultiSelect(selectElement, {
            placeholder: 'Select Disposition',
            search: true,
            selectAll: false,
            max: 1,
        });
        //End OF DISPOSITION MULTI SELECT
        
        // START OF DEFECT MULTI SELECT
        const defectElement = document.getElementById('defect');
        defects.forEach(defect => {
        const option = document.createElement('option');
            option.value = defect.id;
            option.text = defect.name;
            if (defect.id == selected_defect_id) {
                option.selected = true;
            }
            defectElement.appendChild(option);
        });

        new MultiSelect(defectElement, {
            placeholder: 'Select Defect',
            search: true,
            selectAll: false,
            max: 1,
        });
        // END OF DEFECT MULTI SELECT

        document.querySelectorAll('.user-multi-select').forEach(function (selectElement) {

            // // Populate the select element with options
            woData.forEach(user => {
                const option = document.createElement('option');
                option.value = user.PartNo;
                option.setAttribute('data-quantity', user.Qty);
                option.setAttribute('data-location', user.Location);
                option.setAttribute('data-description', user.Name);
                option.text = user.PartNo;
                selectElement.appendChild(option);
            });

            // Initialize MultiSelect after adding options
            new MultiSelect(selectElement, {
                placeholder: 'Select Part Number',
                search: true,
                selectAll: false,
                max: 1,
                onSelect: function(value, text, element) {
                    const selectedPart = woData.find(user => user.PartNo === value);
                    if (selectedPart) {
                        // Populate the input fields with data
                        document.getElementById('quantity').value = selectedPart.Qty;
                        document.getElementById('quantity').max = selectedPart.Qty;
                        document.getElementById('location').value = selectedPart.Location;
                        document.getElementById('description').value = selectedPart.Name;
                    }
                    let formData = {};
                    formData['part_no'] = value;
                    formData['_token'] = '{{ csrf_token() }}';
                    $.ajax({
                        method: "POST",
                        url: '{{ route('mrb.emrb.qtyCur', ['organisation' => $organisation->id]) }}',
                        data: formData,
                        success: function(d) {
                            if (d.status === 2 && d.data.length > 0) { // Check for successful response and data availability
                                const firstItem = d.data[0]; // Access the first item in the data array
                                const currencyInput = $('input[name="currency"]'); // Find the currency input field
                                const unitPriceInput = $('input[name="unit_price"]'); // Find the unit price input field
                                const amountInput = $('input[name="amount"]');
                                if (currencyInput.length && unitPriceInput.length) { // Ensure both input fields exist
                                    currencyInput.val(firstItem.currency); // Set the currency value
                                    unitPriceInput.val(firstItem.unit_price); // Set the unit price value
                                    var amount = document.getElementById('quantity').value * parseFloat(firstItem.unit_price);
                                    amountInput.val(amount.toFixed(2));
                                } else {
                                    toast(1,"Part Number Data Not Found");
                                }
                            } else if (d.status === 1) {
                                let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                                toast(1, errorMessages);
                            } else {
                                toast(1,"Something Wrong, Please Check Console");
                            }
                        },
                        error: function(xhr, status, error) {
                            // Hide the spinner
                            spinner.addClass('d-none');
                            submitButton.prop('disabled', false);
                            toast(1, "Error:", xhr.status, status, error);
                        }
                    });
                },
            });
        });

        $('#quantity').on('change', function() {
            // Get the current value of quantity and unit price
            let quantity = $(this).val();
            let unitPrice = $('#unit_price').val();

            // Check if unit price is not empty
            if (unitPrice) {
                // Calculate the amount and set it in the amount input field
                let amount = quantity * parseFloat(unitPrice);
                $('#amount').val(amount.toFixed(2)); // Format to 2 decimal places
            } else {
                alert('Unit price is empty. Please ensure the unit price is set.');
            }
        });

        //Add Role Form
        $("#addForm").submit(function(event) {
            // Prevent the default form submission
            event.preventDefault();

            // Show the spinner
            let submitButton = $(this).find('button[type="submit"]');
            let buttonValue = submitButton.val();  // Get the value of the button
            let spinner = submitButton.find('.spinner-border');
            spinner.removeClass('d-none');
            submitButton.prop('disabled', true);

            // Create a FormData object for file handling
            let formData = new FormData();

            // Append form fields from the form data array
            let formDataArray = $(this).serializeArray();
            formDataArray.forEach(function(item) {
                formData.append(item.name, item.value);
            });

            // Append CSRF token
            formData.append('_token', '{{ csrf_token() }}');

            // Append the button value as mrb_form_id
            formData.append('mrb_form_id', buttonValue);

            // Append the file if present
            let fileInput = document.getElementById('file');
            if (fileInput.files.length > 0) {
                formData.append('fileToUpload', fileInput.files[0]);
            }

            // Make the AJAX request
            $.ajax({
                method: "POST",
                url: '{{ route('mrb.emrb.storeEmrbContent', ['organisation' => $organisation->id]) }}',
                data: formData,
                processData: false,  // Prevent jQuery from automatically transforming the FormData object into a query string
                contentType: false,  // Set content type to false for FormData object
                success: function(d) {
                    // Hide the spinner and enable the button
                    spinner.addClass('d-none');
                    submitButton.prop('disabled', false);

                    if (d.status === 2) { 
                        $("#modal").modal('hide');
                        loadTableData();
                        toast(2, d.message);
                    } else if (d.status === 1) {
                        let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                        toast(1, errorMessages);
                    }
                },
                error: function(xhr, status, error) {
                    // Hide the spinner and enable the button
                    spinner.addClass('d-none');
                    submitButton.prop('disabled', false);

                    toast(1, `Error: ${xhr.status} ${status} ${error}`);
                }
            });
        });

        $("#editForm").submit(function(event) {
            // Prevent the default form submission
            event.preventDefault();

            // Show the spinner
            let submitButton = $(this).find('button[type="submit"]');
            let buttonValue = submitButton.val();  // Get the value of the button
            let spinner = submitButton.find('.spinner-border');
            spinner.removeClass('d-none');
            submitButton.prop('disabled', true);

            // Create a FormData object for file handling
            let formData = new FormData();

            // Append form fields from the form data array
            let formDataArray = $(this).serializeArray();
            formDataArray.forEach(function(item) {
                formData.append(item.name, item.value);
            });

            // Append CSRF token
            formData.append('_token', '{{ csrf_token() }}');

            // Append the button value as mrb_form_id
            formData.append('id', content_id);

            // Append the file if present
            let fileInput = document.getElementById('file');
            if (fileInput.files.length > 0) {
                formData.append('fileToUpload', fileInput.files[0]);
            }

            // Make the AJAX request
            $.ajax({
                method: "POST",
                url: '{{ route('mrb.emrb.updateEmrbContent', ['organisation' => $organisation->id]) }}',
                data: formData,
                processData: false,  // Prevent jQuery from automatically transforming the FormData object into a query string
                contentType: false,  // Set content type to false for FormData object
                success: function(d) {
                    // Hide the spinner and enable the button
                    spinner.addClass('d-none');
                    submitButton.prop('disabled', false);
                    if (d.status === 2) { 
                        $("#modal").modal('hide');
                        loadTableData();
                        toast(2, d.message);
                    } else if (d.status === 1) {
                        let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                        toast(1, errorMessages);
                    }
                },
                error: function(xhr, status, error) {
                    // Hide the spinner and enable the button
                    spinner.addClass('d-none');
                    submitButton.prop('disabled', false);

                    toast(1, `Error: ${xhr.status} ${status} ${error}`);
                }
            });
        });

    });
</script>