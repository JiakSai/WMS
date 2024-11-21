<link href="{{asset('css/bootstrapicons-iconpicker.css')}}"  rel="stylesheet">
<script src="{{asset('js/bootstrapicon-iconpicker.min.js')}}"></script>

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Upload IPQA Checksheet</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body">
        <form id="auditForm" enctype="multipart/form-data">
            <input type="hidden" name="id" value="{{ $id }}">

            <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">Upload IPQA Order File</label>
                <div class="col-sm-9">    
                    <div class="input-group">
                        <input type="file" class="form-control" required name="ipqa_file" id="ipqa_file">
                    </div>
                </div> 
            </div>
            
            <div class="row">
                <label class="col-sm-3 col-form-label"></label>
                <div class="col-sm-9">    
                    <label class="text-danger">File Name Must Be As: {{ $file_name }}</label>
                </div> 
            </div>
        </form>
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary" id="submit-btn">Upload
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script src="{{asset('js/multiSelect.js')}}"></script>

<script>


    $("#submit-btn").on('click', function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Show the spinner
        let submitButton = $(this).find('button[type="submit"]');
        let spinner = submitButton.find('.spinner-border');
        spinner.removeClass('d-none');
        submitButton.prop('disabled', true);

        // Create a new FormData object for file uploads
        let formData = new FormData();
        let fileInput = document.getElementById('ipqa_file');

        if (fileInput.files.length > 0) {
            formData.append('ipqa_file', fileInput.files[0]);
        }
        formData.append('id', '{{ csrf_token() }}');
        // Append CSRF token
        formData.append('_token', '{{ csrf_token() }}');
        let idValue = $('input[name="id"]').val();  // Grabbing the value from the hidden input
        formData.append('id', idValue);  // Append id to FormData

        $.ajax({
            method: "POST",
            url: '{{ route('qms.ipqa.psmts.uploadStep2', ['organisation' => $organisation->id]) }}',
            data: formData,
            processData: false, 
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
                    } catch (err){
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