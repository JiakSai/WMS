<link href="{{asset('css/bootstrapicons-iconpicker.css')}}"  rel="stylesheet">
<script src="{{asset('js/bootstrapicon-iconpicker.min.js')}}"></script>

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">NEW IPQA Template</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body">
        <div class="mb-3 row">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Version Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Template File</label>
                <div class="col-sm-9">
                    <input class="form-control" type="file" id="formFile" name="fileToUpload">
                </div>
            </div>
            <!-- New Remarks Input -->
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Remarks</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="remarks" placeholder="Enter remarks">
                </div>
            </div>

            <!-- New Categories Select Box -->
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Categories</label>
                <div class="col-sm-9">
                    <select class="form-select" name="categories" required>
                        <option value="" disabled>Select Category</option>
                        <option value="box_build">Box Build</option>
                        <option value="smt">SMT</option>
                    </select>
                </div>
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

        // Add file input manually
        let fileInput = document.getElementById('formFile');
        if (fileInput.files.length > 0) {
            formData.append('fileToUpload', fileInput.files[0]);
        }

        // Serialize the other form data (text inputs) using serializeArray
        let formDataArray = $(this).serializeArray();
        formDataArray.forEach(function(item) {
            formData.append(item.name, item.value);
        });

        // Append CSRF token
        formData.append('_token', '{{ csrf_token() }}');


        $.ajax({
            method: "POST",
            url: '{{ route('qms.ipqa.temps.store', ['organisation' => $organisation->id]) }}',
            data: formData,
            processData: false, // Important! Prevent jQuery from processing the data
            contentType: false, // Important! Prevent jQuery from setting content type
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 
                        $("#modal").modal('hide');
                        toast(2, d.message);
                    } else if (d.status === 1) {
                        let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                        toast(1, errorMessages);
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