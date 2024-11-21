<link href="{{asset('css/bootstrapicons-iconpicker.css')}}"  rel="stylesheet">
<script src="{{asset('js/bootstrapicon-iconpicker.min.js')}}"></script>

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Create IPQA Checklist Report</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body">
        <div class="mb-3 row">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Categories</label>
                <div class="col-sm-9">
                    <select class="form-select" name="categories" id="category-selection">
                        <option value="" disabled selected>Select Category</option>
                        <option value="box_build">Box Build</option>
                        <option value="smt">SMT</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Template</label>
                <div class="col-sm-9">
                    <label id="template-label">Please Select A Category</label>
                    <select class="form-select d-none" name="templates" id="template-selection">
                        <option value="" disabled>Select Template</option>
                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">Work Order Shift</label>
                <div class="col-sm-9">    
                    <div class="input-group">
                    <select class="form-select" required="" name="shift">
                        <option value="" disabled="" selected="">Select Shift</option>
                        <option value="M">Morning Shift</option>
                        <option value="N">Night Shift</option>
                    </select>
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">Date</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" value='.date("Y-m-d").' name="date">
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">Work Order Type</label>
                <div class="col-sm-9">    
                    <div class="input-group">
                    <select class="form-select" required="" name="wo_type"  id="wo_type" onchange="handleWoTypeChange()">
                        <option value="" disabled="" selected="">-- Select Word Order --</option>
                        <option value="C">Customer WO</option>
                        <option value="S">SMTT WO</option>
                    </select>
                    </div>
                </div>
            </div>
            <div class="mb-3 row d-none" id="wo_div">
                <label class="col-sm-3 col-form-label" id="woLabel">Work Order Number</label>
                <div class="col-sm-9">    
                    <div class="input-group">
                    <input type="text" class="form-control" required name="wo" style="text-transform: uppercase" placeholder="TMSXXXXXX" id="woInput">
                    <button type="button" class="btn btn-outline-theme" onClick="inforln_check()" id="sync">Sync <i class="bi bi-arrow-repeat"></i>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></button>
                    </div>
                </div> 
            </div>
            <div class="mb-3 row" id="woSelection">
            </div>
            <div class="mb-3 row">
                <table id="syncTable">
                </table>
            </div>
        </div>  
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary" id="submit-btn">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script src="{{asset('js/multiSelect.js')}}"></script>

<script>

    function handleWoTypeChange() {
        var wo_type = document.getElementById("wo_type");
        var woLabel = document.getElementById("woLabel");
        var woInput = document.getElementById("woInput");
        var wo_div = document.getElementById("wo_div");
        $("#submit").prop("disabled", true);
        $('#syncTable').html("");
        $('#woSelection').html("");
        $('#woInput').val("");
        if (wo_type.value === "C") {
            // Change label for Customer WO
            woLabel.textContent = "Customer Work Order Number";
            woInput.placeholder = "Customer WO"; // Change placeholder text
        } else if (wo_type.value === "S") {
            // Revert back to default for SMTT WO
            woLabel.textContent = "Work Order Number";
            woInput.placeholder = "TMSXXXXXX"; // Reset placeholder text
            wo_div.classList.remove('d-none');
        }
        wo_div.classList.remove('d-none'); 
    }

    function woSelect(self) {
        $.ajax({
            type: "POST",
            url: '{{ route('qms.ipqa.psmts.getWOWithCustomerWODetails', ['organisation' => $organisation->id]) }}',
            dataType: 'json',
            data: {
                wo: $(self).val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(d) {
                d.status === 2 ? $("#submit").prop("disabled", false): $("#submit").prop("disabled", true);
                $('#syncTable').html(d.data);
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error.message);
                $("#modal").modal('hide');
            }
        });      
    }

    function inforln_check() {
        $("#sync").prop("disabled", true).find("span.spinner-border").removeClass("d-none");
        var wo_type = document.getElementById("wo_type");
        if (wo_type.value === "S") {
            $.ajax({
                type: "POST",
                url: '{{ route('qms.ipqa.psmts.getWorkOrderDetails', ['organisation' => $organisation->id]) }}',
                dataType: 'json',
                data:{
                    wo: $('[name="wo"]').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(d) {
                    d.success === true ? $("#submit").prop("disabled", false): null;
                    if(d.success === false){
                        toast(1, d.message);
                    }
                    $('#syncTable').html(d.data);
                    $("#sync").prop("disabled", false).find("span.spinner-border").addClass("d-none");
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error.message);
                    $("#modal").modal('hide');
                }
                });  
        } else {
            $('#syncTable').html('');
            $.ajax({
                type: "POST",
                url: '{{ route('qms.ipqa.psmts.getCustomerWorkOrderDetails', ['organisation' => $organisation->id]) }}',
                dataType: 'json',
                data:{
                    cus_wo: $('[name="wo"]').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(d) {
                    //d.status === 2 ? $("#submit").prop("disabled", false): null;
                    $('#woSelection').html(d.data);
                    $("#sync").prop("disabled", false).find("span.spinner-border").addClass("d-none");
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error.message);
                    $("#modal").modal('hide');
                }
            }); 
        }
    }

    $('#category-selection').change(function() {
        let category = $(this).val(); // Get selected category

        // Clear any previous templates
        $('#template-selection').empty();
        
        // Add the default "Select Template" option
        $('#template-selection').append('<option value="" disabled selected>Select Template</option>');

        // Show the template select box and label while loading
        $('#template-selection').removeClass('d-none');

        // Perform the AJAX request to fetch templates based on the selected category
        $.ajax({
            url: '{{ route('qms.ipqa.psmts.getTemplate', ['organisation' => $organisation->id]) }}', // Adjust this route based on your Laravel setup
            method: 'POST',
            data: {
                category: category,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    // Loop through the data and append each template as an option
                    response.data.forEach(function(item) {
                        $('#template-selection').append(
                            `<option value="${item.id}">${item.version_name}</option>`
                        );
                    });
                    // Update the label after templates are loaded
                    $('#template-label').addClass('d-none');
                } else {
                    // Show message if no templates found
                    $('#template-selection').addClass('d-none');
                    $('#template-label').text(response.message || 'No templates available.');
                }
            },
            error: function() {
                $('#template-label').text('Error loading templates.');
            }
        });
    });

    

    $("#addForm").submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Show the spinner
        let submitButton = $(this).find('button[type="submit"]');
        let spinner = submitButton.find('.spinner-border');
        spinner.removeClass('d-none');
        submitButton.prop('disabled', true);

        // Create a new FormData object for file uploads
        let formData = new FormData();


        $('#syncTable tbody tr td').each(function(index) {
            var columnName = $('#syncTable thead th').eq(index).text().toLowerCase();
            formData.append(columnName, $(this).text());
        });
        // Serialize the other form data (text inputs) using serializeArray
        let formDataArray = $(this).serializeArray();
        formDataArray.forEach(function(item) {
            formData.append(item.name, item.value);
        });

        // Append CSRF token
        formData.append('_token', '{{ csrf_token() }}');


        $.ajax({
            method: "POST",
            url: '{{ route('qms.ipqa.psmts.store', ['organisation' => $organisation->id]) }}',
            data: formData,
            processData: false, // Important! Prevent jQuery from processing the data
            contentType: false, // Important! Prevent jQuery from setting content type
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 
                        $("#modal").modal('hide');
                        ipqaDataTable.ajax.reload();
                        toast(2, d.message);
                    } else if (d.status === 1) {
                        try{
                            let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                            toast(1, errorMessages);                            
                        } catch (e){
                            toast(1, d.message);
                        }

                    }
            },
            error: function(xhr, status, error) {

                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);
                
                toast(1,"Error:", xhr.status, status, error);
            }
        });
    });

    $(function(){
        $('.iconpicker').iconpicker();
    });
</script>