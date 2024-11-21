<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Assign User</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Permissions Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" value="{{ $permission->name }}" readonly>
                    <input type="hidden" class="form-control" name="id" value="{{ $permission->id }}" readonly>
                    <input type="hidden" class="form-control" name="main_id" value="{{ $mainModule->id }}" readonly>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-md-12 mb-2">
                    <table id="userTable" class="table table-bordered text-center role-order-list">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>Role</td>
                                <td>Delete</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedRoles as $index => $selectedRole)
                            <tr>
                                <td class="col-md-1" style="padding-top: 15px">1</td>
                                <td class="col-md-7">
                                    <select class="form-select role-select" name="role[]">
                                        <option value="" disabled>Select Organisation</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" 
                                                @if($role->id == $selectedRole) selected @endif>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-md-1" style="padding-top: 11px">
                                    <a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="col-md-1" style="padding-top: 15px">{{ count($selectedRoles) + 1 }}</td>
                                <td class="col-md-7">
                                    <select class="form-select role-select" name="role[]">
                                        <option value="" selected disabled>Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
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
        <button type="submit" class="btn btn-primary" id="submitButton">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    $(document).ready(function () {
        var roleCounter = 1;

        function addRoleRow() {
            if ($("select.role-select option:not(:selected):not(:disabled)").length === 0) {
                return; // Do not add a new row if all options are selected
            }

            roleCounter++;
            var newRow = $("<tr>");
            var cols = "";

            cols += '<td class="col-md-1" style="padding-top: 15px">' + roleCounter + '</td>';
            cols += '<td class="col-md-7"><select class="form-select role-select" name="role[]"><option value="" selected disabled>Select Role</option>@foreach($roles as $role)<option value="{{ $role->id }}">{{ $role->name }}</option>@endforeach</select></td>';
            cols += '<td class="col-md-1" style="padding-top: 11px"><a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a></td>';

            newRow.append(cols);
            $("table.role-order-list").append(newRow);
            updateUserOptions();
        }

        function updateUserRowNumbers() {
            $("table.role-order-list tbody tr").each(function(index) {
                $(this).find("td:first").text(index + 1);
                $(this).find("input[type='radio']").val(index);
            });
        }

        function updateUserOptions() {
            var selectedOrganisation = [];
            $("select.role-select").each(function() {
                if ($(this).val()) {
                    selectedOrganisation.push($(this).val());
                }
            });

            $("select.role-select").each(function() {
                var currentSelect = $(this);
                currentSelect.find('option').each(function() {
                    if ($(this).val() === "") {
                        $(this).toggle(selectedOrganisation.length < $("select.role-select option").length - 1);
                    } else if (selectedOrganisation.includes($(this).val()) && $(this).val() !== currentSelect.val()) {
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
            var lastRow = $("table.role-order-list tbody tr:last");
            var lastSelect = lastRow.find('.role-select');
            if (lastSelect.val() && $("select.role-select option:not(:selected):not(:disabled)").length > 0) {
                addRoleRow();
            }
        }

        $("table.role-order-list").on("change", ".role-select", function () {
            updateUserOptions();
        });

        $("table.role-order-list").on("click", ".deleteRow", function (event) {
            event.preventDefault();
            var row = $(this).closest("tr");
            if (!$(this).hasClass('disabled')) {
                row.remove();
                roleCounter -= 1;
                updateUserRowNumbers();
                updateUserOptions();
            }
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

            formData['_token'] = '{{ csrf_token() }}';

            $.ajax({
                method: "POST",
                url: '{{ route('rbp.permission.store', ['organisation' => $organisation->id]) }}',
                data: formData,
                success: function(d) {
                    // Hide the spinner
                    spinner.addClass('d-none');
                    submitButton.prop('disabled', false);

                    if (d.status === 2) { 

                        $("#modal").modal('hide');

                        permDataTable.ajax.reload();
                        
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

        updateUserOptions();
    });
</script>