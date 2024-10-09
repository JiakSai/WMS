<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Tab</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Main Module Group</label>
                <div class="col-sm-9">
                    <select class="form-select" name="mainGroup" id="mainGroup" autofocus>
                        <option value="" selected disabled>Select Main Module Group</option>
                        @foreach($mainGroups as $mainGroup)
                            <option value="{{ $mainGroup->id }}" data-code="{{ $mainGroup->code }}">{{ $mainGroup->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Sub Module Group</label>
                <div class="col-sm-9">
                    <select class="form-select" name="subGroup" id="subGroup" disabled>
                        <option value="" selected disabled>Select Sub Module Group</option>
                        @foreach($subGroups as $subGroup)
                            <option value="{{ $subGroup->id }}" data-group="{{ $subGroup->group }}" data-code="{{ $subGroup->code }}">{{ $subGroup->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Tab Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" id="tabName">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Tab Code</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <span class="input-group-text" id="subGroupPrefix"></span>
                        <input type="text" class="form-control text-lowercase" name="code" id="tabCode" maxlength="4" required>
                        <span class="input-group-text">s</span>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Route</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="route" id="tabRoute" readonly>
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
                            <td>Migration</td>
                            <td id="migrationName"></td>
                            <td id="migrationPath"></td>
                        </tr>
                        <tr>
                            <td>Model</td>
                            <td id="modelName"></td>
                            <td id="modelPath"></td>
                        </tr>
                        <tr>
                            <td>Controller</td>
                            <td id="controllerName"></td>
                            <td id="controllerPath"></td>
                        </tr>
                        <tr>
                            <td>View</td>
                            <td id="viewName"></td>
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
        let subGroupSelect = document.getElementById('subGroup');
        let codeInput = document.getElementById('tabCode');
        let routeInput = document.getElementById('tabRoute');

        let selectedSubGroup = subGroupSelect.options[subGroupSelect.selectedIndex];
        let subGroupCode = selectedSubGroup ? selectedSubGroup.getAttribute('data-code').replace(/_/g, '.') : '';
        let tabCode = codeInput.value.toLowerCase() + 's';

        if (tabCode.length === 5) {
            routeInput.value = `${subGroupCode}.${tabCode}.index`;
        } else {
            routeInput.value = '';
        }
    }

    document.getElementById('mainGroup').addEventListener('change', function() {
        let mainGroupId = this.value;
        let subGroupSelect = document.getElementById('subGroup');

        // Clear previous options
        subGroupSelect.innerHTML = '<option value="" selected disabled>Select Sub Module Group</option>';

        // Filter submodule groups based on the selected main group
        @foreach($subGroups as $subGroup)
            if ({{ $subGroup->group }} == mainGroupId) {
                let option = document.createElement('option');
                option.value = '{{ $subGroup->id }}';
                option.textContent = '{{ $subGroup->name }}';
                option.setAttribute('data-code', '{{ $subGroup->code }}');
                subGroupSelect.appendChild(option);
            }
        @endforeach

        subGroupSelect.disabled = false;
    });

    document.getElementById('subGroup').addEventListener('change', function() {
        let subGroupCode = this.options[this.selectedIndex].getAttribute('data-code');
        document.getElementById('subGroupPrefix').textContent = subGroupCode + '_';
    });

    document.getElementById('tabCode').addEventListener('input', function() {
        this.value = this.value.replace(/[^a-z]/g, '');
        generateRoute();
    });

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

        // Concatenate the sub group prefix and the code before sending
        let subGroupPrefix = document.getElementById('subGroupPrefix').textContent;
        formData['code'] = `${subGroupPrefix}${formData['code']}s`;

        formData['_token'] = '{{ csrf_token() }}';

        $.ajax({
            method: "POST",
            url: '{{ route('sys.modu.tabms.store', ['organisation' => $organisation->id]) }}',
            data: formData,
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 

                    // Update the table with the new data
                    $('#controllerName').text(d.controllerName);
                    $('#controllerPath').text(d.controllerPath);
                    $('#migrationName').text(d.migrationName);
                    $('#migrationPath').text(d.migrationPath);
                    $('#modelName').text(d.modelName);
                    $('#modelPath').text(d.modelPath);
                    $('#viewName').text(d.viewName);
                    $('#viewPath').text(d.viewPath);

                    // Hide the form and submit button, show the table
                    $('#formContent').hide();
                    $('#submitButton').hide();
                    $('#infoTable').show();

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