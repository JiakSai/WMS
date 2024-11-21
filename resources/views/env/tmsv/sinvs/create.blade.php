<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Upload File</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm" enctype="multipart/form-data">
    @csrf <!-- Laravel CSRF token for security -->
    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Upload</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" name="file" accept=".xlsx, .xls, .csv" required>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary" id="submitButton">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
$("#addForm").submit(function(event) {
    event.preventDefault();

    // Show the spinner and disable the submit button
    let submitButton = $(this).find('button[type="submit"]');
    let spinner = submitButton.find('.spinner-border');
    spinner.removeClass('d-none');
    submitButton.prop('disabled', true);

    // Prepare FormData for file upload
    let formData = new FormData(this);
    formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token

    $.ajax({
        method: "POST",
        url: '{{ route('env.tmsv.sinvs.import', ['organisation' => $organisation->id]) }}',
        data: formData,
        processData: false,  
        contentType: false,  
        success: function(response) {
            // Hide the spinner
            spinner.addClass('d-none');
            submitButton.prop('disabled', false);

            if (response.status === 2) { 
                $("#modal").modal('hide');
                toast(2, response.message); // Success toast
            } else if (response.status === 1) {
                let errorMessage = response.message;
                toast(1, errorMessage); // Error toast
            }
        },
        error: function(xhr, status, error) {
            // Hide the spinner and show error toast
            spinner.addClass('d-none');
            submitButton.prop('disabled', false);
            toast(1, `Error: ${xhr.status} - ${xhr.responseText || status}`);
        }
    });
});
</script>
