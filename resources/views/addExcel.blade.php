<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Group</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body">
        <div class="mb-3 row">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Upload File</label>
                <div class="col-sm-9">
                    <input type="file" class="form-control" name="file">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>

    $("#addForm").submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Create a FormData object
        let formData = new FormData(this);

        // Append the CSRF token
        formData.append('_token', '{{ csrf_token() }}');

        $.ajax({
            method: "POST",
            url: '{{ route('addExcelSubmit', ['warehouse' => $warehouse->id]) }}',
            data: formData,
            processData: false,
            contentType: false,
            success: function(d) {
                if (d.status === 2) { 
                    $("#modal").modal('hide');
                    datatable.ajax.reload();
                    toast(2, d.message);
                } else if (d.status === 1) {
                    let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                    toast(1, errorMessages);
                }
            },
            error: function(xhr, status, error) {
                toast(1, "Error:", xhr.status, status, error);
            }
        });
    });
</script>