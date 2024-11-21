<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Tab</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Main Module Group</label>
                <div class="col-sm-9">
                    <div class="col-sm-9">
                        <select class="form-select" name="mainGroupDisplay" disabled>
                            <option value="{{ $selectedMainGroup->id }}" selected>{{ $selectedMainGroup->name }}</option>
                        </select>
                        <input type="hidden" name="mainGroup" value="{{ $selectedMainGroup->id }}">
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Sub Module Group</label>
                <div class="col-sm-9">
                    <select class="form-select" name="subGroupDisplay" disabled>
                        <option value="{{ $selectedSubGroup->id }}" selected>{{ $selectedSubGroup->name }}</option>
                    </select>
                    <input type="hidden" name="subGroup" value="{{ $selectedSubGroup->id }}">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Tab Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" value="{{ $tabModule->name}}">
                    <input type="hidden" name="id" value="{{ $tabModule->id }}">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Tab Code</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control text-lowercase" name="code" value="{{ $tabModule->code }} " readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Route</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="route" value=" {{ $tabModule->route }}" readonly>
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
            url: '{{ route('sys.modu.tabms.update', ['organisation' => $organisation->id]) }}',
            data: formData,
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 

                    $("#modal").modal('hide');
                    tabDataTable.ajax.reload();
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
</script>