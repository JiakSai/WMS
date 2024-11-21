<!-- Edit Modal -->
<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit User</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="editForm">
    @csrf
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="userTabs" role="tablist">
            <li class="nav-user" role="presentation">
                <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab">User</button>
            </li>
            <li class="nav-user" role="presentation">
                <button class="nav-link" id="permission-tab" data-bs-toggle="tab" data-bs-target="#permission" type="button" role="tab">Main Module Permissions</button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="userTabsContent">
            <!-- User Tab -->
            <div class="tab-pane fade show active" id="user" role="tabpanel">
                <div class="mb-2 row">
                    <div class="col-md-2 mb-2">
                        <label class="col-form-label">Employee ID</label>
                        <input type="number" class="form-control" name="username" value="{{ $user->username }}" readonly>
                    </div>
                    <div class="col-md-5 mb-2">
                        <label class="col-form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="col-form-label">Department</label>
                        <select class="form-select" name="group" data-placeholder="Select Group">
                            <option value="" selected disabled>Select Department</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" 
                                    @if($group->id == $user->group) selected @endif>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="col-form-label">Role</label>
                        <select class="form-select" name="role" data-placeholder="Select Role">
                            <option value="" selected disabled>Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                    @if($role->id == $user->role) selected @endif>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-md-7 mb-2">
                        <label class="col-form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $user->email_address }}">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="col-form-label">Phone Number</label>
                        <input type="number" class="form-control" name="phone_number" value="{{ $user->phone_number }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="col-form-label">Telegram ID</label>
                        <input type="text" class="form-control" name="telegram_id" value="{{ $user->telegram_id }}">
                    </div>
                </div>
                <div class="mb-2 row">
                    <div class="col-md-12 mb-2">
                        <table id="userTable" class="table table-bordered text-center user-order-list">
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Organisation</td>
                                    <td>Default</td>
                                    <td>Delete</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedOrganisations as $index => $selectedOrganisation)
                                <tr>
                                    <td class="col-md-1" style="padding-top: 15px">{{ $index + 1 }}</td>
                                    <td class="col-md-7">
                                        <select class="form-select organisation-select" name="organisation[]">
                                            <option value="" disabled>Select Organisation</option>
                                            @foreach($organisations as $org)
                                                <option value="{{ $org->id }}" 
                                                    @if($org->id == $selectedOrganisation) selected @endif>
                                                    {{ $org->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="col-md-1" style="padding-top: 13px">
                                        <input type="radio" class="form-check-input default-organisation" name="default_organisation" value="{{ $index }}" 
                                            @if($defaultOrganisation && $defaultOrganisation->id == $selectedOrganisation) checked @endif>
                                    </td>
                                    <td class="col-md-1" style="padding-top: 11px">
                                        <a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                <tr id="addUserRow"> <!-- Changed ID to addUserRow -->
                                    <td class="col-md-1" style="padding-top: 15px">{{ count($selectedOrganisations) + 1 }}</td>
                                    <td class="col-md-7">
                                        <select class="form-select organisation-select" name="organisation[]">
                                            <option value="" selected disabled>Select Organisation</option>
                                            @foreach($organisations as $org)
                                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="col-md-1" style="padding-top: 13px">
                                        <input type="radio" class="form-check-input default-organisation" name="default_organisation" value="" disabled>
                                    </td>
                                    <td class="col-md-1" style="padding-top: 11px">
                                        <a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Permission Tab -->
            <div class="tab-pane fade" id="permission" role="tabpanel">
                <div class="col-md-12 mb-2">
                    <table id="permissionTable" class="table table-bordered text-center permission-order-list">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>Main Module Permissions</td>
                                <td>Delete</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedPermissions as $index => $selectedPermission)
                            <tr>
                                <td class="col-md-1" style="padding-top: 15px">{{ $index + 1 }}</td> <!-- Dynamic Row Number -->
                                <td class="col-md-7">
                                    <select class="form-select permission-select" name="permission[]">
                                        <option value="" disabled>Select Main Module</option>
                                        @foreach($mainModules as $mainModule)
                                            <option value="{{ $mainModule->id }}" 
                                                @if($mainModule->id == $selectedPermission) selected @endif>
                                                {{ $mainModule->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-md-1" style="padding-top: 11px">
                                    <a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            <tr id="addPermissionRow"> <!-- Changed ID to addPermissionRow -->
                                <td class="col-md-1" style="padding-top: 15px">{{ count($selectedPermissions) + 1 }}</td>
                                <td class="col-md-7">
                                    <select class="form-select permission-select" name="permission[]">
                                        <option value="" selected disabled>Select Main Module</option>
                                        @foreach($mainModules as $mainModule)
                                            <option value="{{ $mainModule->id }}">{{ $mainModule->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-md-1" style="padding-top: 11px">
                                    <a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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

<!-- JavaScript -->
<script>
$(document).ready(function () {

    var userCounter = $("table.user-order-list tbody tr").length - 1; // Initialize counter based on existing rows
    var permissionCounter = $("table.permission-order-list tbody tr").length - 1; // Initialize counter based on existing rows

    function addUserRow() {
        if ($("select.organisation-select option:not(:selected):not(:disabled)").length === 0) {
            return; // Do not add a new row if all options are selected
        }

        userCounter++;
        var newRow = $("<tr>");
        var cols = "";

        cols += '<td class="col-md-1" style="padding-top: 15px">' + userCounter + '</td>';
        cols += '<td class="col-md-7"><select class="form-select organisation-select" name="organisation[]"><option value="" selected disabled>Select Organisation</option>@foreach($organisations as $org)<option value="{{ $org->id }}">{{ $org->name }}</option>@endforeach</select></td>';
        cols += '<td class="col-md-1" style="padding-top: 15px"><input type="radio" class="form-check-input default-organisation" name="default_organisation" value="' + (userCounter - 1) + '" disabled></td>';
        cols += '<td class="col-md-1" style="padding-top: 11px"><a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a></td>';

        newRow.append(cols);
        $("table.user-order-list tbody").append(newRow);
        updateOrganisationOptions();
        updateUserRowNumbers();
    }

    function addPermissionRow() {
        if ($("select.permission-select option:not(:selected):not(:disabled)").length === 0) {
            return; // Do not add a new row if all options are selected
        }

        permissionCounter++;
        var newRow = $("<tr>");
        var cols = "";

        cols += '<td class="col-md-1" style="padding-top: 15px">' + permissionCounter + '</td>';
        cols += '<td class="col-md-7"><select class="form-select permission-select" name="permission[]"><option value="" selected disabled>Select Main Module</option>@foreach($mainModules as $mainModule)<option value="{{ $mainModule->id }}">{{ $mainModule->name }}</option>@endforeach</select></td>';
        cols += '<td class="col-md-1" style="padding-top: 11px"><a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a></td>';

        newRow.append(cols);
        $("table.permission-order-list tbody").append(newRow);
        updatePermissionOptions();
        updatePermissionRowNumbers();
    }

    function updateUserRowNumbers() {
        $("table.user-order-list tbody tr").each(function(index) {
            $(this).find("td:first").text(index + 1);
            $(this).find("input[type='radio'].default-organisation").val(index);
        });
    }

    function updatePermissionRowNumbers() {
        $("table.permission-order-list tbody tr").each(function(index) {
            $(this).find("td:first").text(index + 1);
        });
    }

    function updateOrganisationOptions() {
        var selectedOrganisations = [];
        $("select.organisation-select").each(function() {
            if ($(this).val()) {
                selectedOrganisations.push($(this).val());
            }
        });

        $("select.organisation-select").each(function() {
            var currentSelect = $(this);
            currentSelect.find('option').each(function() {
                if ($(this).val() === "") {
                    $(this).show(); // Always show the "Select Organisation" option
                } else if (selectedOrganisations.includes($(this).val()) && $(this).val() !== currentSelect.val()) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });

            // Enable/disable the default organisation radio button based on selection
            var defaultRadio = currentSelect.closest('tr').find('.default-organisation');
            var deleteButton = currentSelect.closest('tr').find('.deleteRow');
            if (currentSelect.val()) {
                defaultRadio.prop('disabled', false);
                deleteButton.removeClass('disabled').attr('tabindex', '0');
            } else {
                defaultRadio.prop('disabled', true);
                defaultRadio.prop('checked', false);
                deleteButton.addClass('disabled').attr('tabindex', '-1');
            }
        });

        // Add a new row only if the last row has a selected organisation
        var lastRow = $("table.user-order-list tbody tr:last");
        var lastSelect = lastRow.find('.organisation-select');
        if (lastSelect.val() && $("select.organisation-select option:not(:selected):not(:disabled)").length > 0) {
            addUserRow();
        }
    }

    function updatePermissionOptions() {
        var selectedPermissions = [];
        $("select.permission-select").each(function() {
            if ($(this).val()) {
                selectedPermissions.push($(this).val());
            }
        });

        $("select.permission-select").each(function() {
            var currentSelect = $(this);
            currentSelect.find('option').each(function() {
                if ($(this).val() === "") {
                    $(this).show(); // Always show the "Select Main Module" option
                } else if (selectedPermissions.includes($(this).val()) && $(this).val() !== currentSelect.val()) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });

            var deleteButton = currentSelect.closest('tr').find('.deleteRow');
            if (currentSelect.val()) {
                deleteButton.removeClass('disabled').attr('tabindex', '0');
            } else {
                deleteButton.addClass('disabled').attr('tabindex', '-1');
            }
        });

        // Add a new row only if the last row has a selected permission
        var lastRow = $("table.permission-order-list tbody tr:last");
        var lastSelect = lastRow.find('.permission-select');
        if (lastSelect.val() && $("select.permission-select option:not(:selected):not(:disabled)").length > 0) {
            addPermissionRow();
        }
    }

    // Event listeners for Organisation Select
    $("table.user-order-list").on("change", ".organisation-select", function () {
        updateOrganisationOptions();
    });

    // Event listeners for Permission Select
    $("table.permission-order-list").on("change", ".permission-select", function () {
        updatePermissionOptions();
    });

    // Event listeners for Delete Buttons in Organisation Table
    $("table.user-order-list").on("click", ".deleteRow", function (event) {
        event.preventDefault();
        var row = $(this).closest("tr");
        if (!$(this).hasClass('disabled')) {
            row.remove();
            userCounter--;
            updateUserRowNumbers();
            updateOrganisationOptions();
        }
    });

    // Event listeners for Delete Buttons in Permission Table
    $("table.permission-order-list").on("click", ".deleteRow", function (event) {
        event.preventDefault();
        var row = $(this).closest("tr");
        if (!$(this).hasClass('disabled')) {
            row.remove();
            permissionCounter--;
            updatePermissionRowNumbers();
            updatePermissionOptions();
        }
    });

    // Handle Form Submission for Edit
    $("#editForm").submit(function(event) { // Changed to editForm
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
            method: "POST", // Using POST due to form method spoofing with _method
            url: '{{ route('sys.usrm.users.update', ['organisation' => $organisation->id, 'user' => $user->id]) }}', // Updated URL with user ID
            data: formData,
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 
                    $("#modal").modal('hide');
                    userDataTable.ajax.reload();
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

                toast(1, "Error:", xhr.status, status, error);
            }
        });
    });

    // Initialize options and counters on page load
    updateOrganisationOptions();
    updateUserRowNumbers();
    updatePermissionOptions();
    updatePermissionRowNumbers();
});
</script>