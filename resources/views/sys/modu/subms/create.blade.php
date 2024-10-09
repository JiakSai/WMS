<link href="{{asset('css/bootstrapicons-iconpicker.css')}}" rel="stylesheet">
<script src="{{asset('js/bootstrapicon-iconpicker.min.js')}}"></script>

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Sub Module</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Main Module Group</label>
                <div class="col-sm-9">
                    <select class="form-select" name="group" id="moduleGroup" autofocus>
                        <option value="" selected disabled>Select Main Module Group</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" data-code="{{ $group->code }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Sub Module Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Sub Module Code</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <span class="input-group-text" id="groupPrefix"></span>
                        <input type="text" class="form-control text-lowercase" name="code" id="moduleCode" minlength="4" maxlength="4" pattern="[a-z]{4}" required>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Description</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="description">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Route</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="route" id="moduleRoute" readonly>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Mobile</label>
                <div class="col-sm-9">
                    <div class="form-check form-switch pt-1">
                        <input type="checkbox" class="form-check-input" name="mobile" role="switch" id="flexSwitchCheckDefault">
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Icon</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control iconpicker" name="icon" aria-label="Icone Picker" aria-describedby="basic-addon1">
                </div>
            </div>
        </div>
        <div class="mb-2 row" id="infoTable" style="display: none;">
            <div class="col-md-12 mb-2">
                <table id="myTable" class="table table-bordered text-center order-list">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Name</td>
                            <td>Path</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>View</td>
                            <td>index.blade.php</td>
                            <td id="viewPath"></td>
                        </tr>
                    </tbody>
                </table>
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
    function generateRoute() {
        let groupSelect = document.getElementById('moduleGroup');
        let codeInput = document.getElementById('moduleCode');
        let routeInput = document.getElementById('moduleRoute');

        let selectedGroup = groupSelect.options[groupSelect.selectedIndex];
        let groupCode = selectedGroup ? selectedGroup.getAttribute('data-code') : '';
        let moduleCode = codeInput.value.toLowerCase();

        if (groupCode && moduleCode.length === 4) {
            routeInput.value = `${groupCode}.${moduleCode}.index`;
        } else {
            routeInput.value = '';
        }
    }

    function updateModuleCode() {
        let groupSelect = document.getElementById('moduleGroup');
        let codeInput = document.getElementById('moduleCode');
        let groupPrefix = document.getElementById('groupPrefix');

        let selectedGroup = groupSelect.options[groupSelect.selectedIndex];
        let groupCode = selectedGroup ? selectedGroup.getAttribute('data-code') : '';

        if (groupCode) {
            groupPrefix.textContent = `${groupCode}_`;
            codeInput.readOnly = false;
        } else {
            groupPrefix.textContent = '';
            codeInput.value = '';
            codeInput.readOnly = true;
        }
    }

    // Ensure the value is always lowercase and only letters
    document.getElementById('moduleCode').addEventListener('input', function() {
        this.value = this.value.replace(/[^a-z]/g, '').toLowerCase();
    });

    document.getElementById('moduleGroup').addEventListener('change', function() {
        updateModuleCode();
        generateRoute();
    });

    document.getElementById('moduleCode').addEventListener('input', generateRoute);

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

        // Concatenate the group prefix and the code before sending
        let groupSelect = document.getElementById('moduleGroup');
        let selectedGroup = groupSelect.options[groupSelect.selectedIndex];
        let groupCode = selectedGroup ? selectedGroup.getAttribute('data-code') : '';
        formData['code'] = `${groupCode}_${formData['code']}`;

        formData['_token'] = '{{ csrf_token() }}';

        $.ajax({
            method: "POST",
            url: '{{ route('sys.modu.subms.store', ['organisation' => $organisation->id]) }}',
            data: formData,
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 

                    // Update the table with the new data
                    $('#viewPath').text(d.viewPath);

                    // Hide the form and submit button, show the table
                    $('#formContent').hide();
                    $('#submitButton').hide();
                    $('#infoTable').show();

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