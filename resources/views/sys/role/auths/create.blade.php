<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Permission</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label" >Permission Type</label>
                <div class="col-sm-9">
                    <select class="form-select" name="type" id="permissionType">
                        <option value="" selected disabled>Select</option>
                        <option value="view">View</option>
                        <option value="create">Create</option>
                        <option value="edit">Edit</option>
                        <option value="delete">Delete</option>
                        <option value="approve">Approve</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Main Module</label>
                <div class="col-sm-9">
                    <select class="form-select" name="main_module" id="mainModule">
                        <option value="" selected disabled>Select</option>
                        @foreach($main_modules as $main_module)
                            <option value="{{ $main_module->id }}">{{ $main_module->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Permission Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" id="permissionName" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Description</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="description">
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
        // Prevent the default form submission
        event.preventDefault();

        // Show the spinner
        let submitButton = $(this).find('button[type="submit"]');
        let spinner = submitButton.find('.spinner-border');
        spinner.removeClass('d-none');
        submitButton.prop('disabled', true);

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
            url: '{{ route('sys.role.auths.store', ['organisation' => $organisation->id]) }}',
            data: formData,
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 

                    $("#modal").modal('hide');

                    authDataTable.ajax.reload();
                    
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

    function updatePermissionName() {
        var permissionTypeText = $('#permissionType option:selected').text();
        var mainModuleText = $('#mainModule option:selected').text();
        
        if (permissionTypeText !== 'Select' && mainModuleText !== 'Select') {
            $('#permissionName').val(permissionTypeText + ' - ' + mainModuleText);
        } else {
            $('#permissionName').val('');
        }
    }

    $('#permissionType, #mainModule').on('change', updatePermissionName);
</script>