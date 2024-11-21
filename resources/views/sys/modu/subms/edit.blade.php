<link href="{{asset('css/bootstrapicons-iconpicker.css')}}" rel="stylesheet">
<script src="{{asset('js/bootstrapicon-iconpicker.min.js')}}"></script>

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Sub Module</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Sub Module Group</label>
                <div class="col-sm-9">
                    <select class="form-select" name="groupDisplay" disabled>
                        <option value="{{ $selectedGroup->id }}" selected>{{ $selectedGroup->name }}</option>
                    </select>
                    <input type="hidden" name="group" value="{{ $selectedGroup->id }}">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label" autofocus>Sub Module Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" value="{{ $subModule->name }}">
                    <input type="hidden" name="id" value="{{ $subModule->id }}">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Sub Module Code</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <input type="text" class="form-control text-lowercase" name="code" value="{{ $subModule->code }}" readonly>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Description</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="description" value="{{ $subModule->description }}">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Route</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="route" id="moduleRoute" value="{{ $subModule->route}}" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Mobile</label>
                <div class="col-sm-9">
                    <div class="form-check form-switch pt-1">
                        <input type="checkbox" class="form-check-input" name="mobile" role="switch" id="flexSwitchCheckDefault" {{$checked}}>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Icon</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control iconpicker" name="icon" aria-label="Icone Picker" aria-describedby="basic-addon1" value="{{ $subModule->icon }}">
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
            url: '{{ route('sys.modu.subms.update', ['organisation' => $organisation->id]) }}',
            data: formData,
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 
                    $("#modal").modal('hide');
                    subDataTable.ajax.reload();
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