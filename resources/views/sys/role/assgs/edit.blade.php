{{-- resources/views/sys/role/assgs/edit.blade.php --}}
<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Role Permissions</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="editForm" method="POST">
    @csrf
    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
        <!-- Role Selection -->
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">User Role</label>
            <div class="col-sm-9">
                <select class="form-select" name="role_id" id="role" disabled>
                    <option value="{{ $role->id }}" selected>{{ $role->name }}</option>
                </select>
            </div>
        </div>
        <!-- Permissions Table -->
        <div class="mb-2 row">
            <div class="col-md-12 mb-2">
                <table id="myTable" class="table table-bordered text-center order-list">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Permissions</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($role->permissions as $index => $permissionId)
                            <tr>
                                <td class="col-md-1" style="padding-top: 15px">{{ $index + 1 }}</td>
                                <td class="col-md-8">
                                    <select class="form-select permission-select" name="permissions[]">
                                        <option value="" disabled>Select Permission</option>
                                        @foreach($permissions as $permission)
                                            <option value="{{ $permission->id }}" {{ $permission->id == $permissionId ? 'selected' : '' }}>
                                                {{ $permission->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-md-1" style="padding-top: 11px">
                                    <a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        @if(empty($role->permissions))
                            <tr>
                                <td class="col-md-1" style="padding-top: 15px">1</td>
                                <td class="col-md-8">
                                    <select class="form-select permission-select" name="permissions[]">
                                        <option value="" selected disabled>Select Permission</option>
                                        @foreach($permissions as $permission)
                                            <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-md-1" style="padding-top: 11px">
                                    <a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary">
            Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>
<script>
    $(document).ready(function () {
        var counter = $("table.order-list tbody tr").length;

        function addRow() {
            if ($("select.permission-select option:not(:selected):not(:disabled)").length === 0) {
                return; // Do not add a new row if all options are selected
            }

            counter++;
            var newRow = $("<tr>");
            var cols = "";

            cols += '<td class="col-md-1" style="padding-top: 15px">' + counter + '</td>';
            cols += '<td class="col-md-8"><select class="form-select permission-select" name="permissions[]"><option value="" selected disabled>Select Permission</option>@foreach($permissions as $permission)<option value="{{ $permission->id }}">{{ $permission->name }}</option>@endforeach</select></td>';
            cols += '<td class="col-md-1" style="padding-top: 11px"><a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a></td>';

            newRow.append(cols);
            $("table.order-list").append(newRow);
            updatePermissionOptions();
        }

        function updateRowNumbers() {
            $("table.order-list tbody tr").each(function(index) {
                $(this).find("td:first").text(index + 1);
            });
            counter = $("table.order-list tbody tr").length;
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
                        $(this).toggle(selectedPermissions.length < $("select.permission-select option").length - 1);
                    } else if (selectedPermissions.includes($(this).val()) && $(this).val() !== currentSelect.val()) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });

                // Enable/disable the delete button based on selection
                var deleteButton = currentSelect.closest('tr').find('.deleteRow');
                if (currentSelect.val()) {
                    deleteButton.removeClass('disabled').attr('tabindex', '0');
                } else {
                    deleteButton.addClass('disabled').attr('tabindex', '-1');
                }
            });

            // Add a new row only if the last row has a selected permission
            var lastRow = $("table.order-list tbody tr:last");
            var lastSelect = lastRow.find('.permission-select');
            if (lastSelect.val() && $("select.permission-select option:not(:selected):not(:disabled)").length > 0) {
                addRow();
            }
        }

        $("table.order-list").on("change", ".permission-select", function () {
            updatePermissionOptions();
        });

        $("table.order-list").on("click", ".deleteRow", function (event) {
            event.preventDefault();
            var row = $(this).closest("tr");
            if (!$(this).hasClass('disabled')) {
                row.remove();
                updateRowNumbers();
                updatePermissionOptions();
            }
        });

        // Add Form Submission
        $("#editForm").submit(function(event) {
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

            $.ajax({
                method: "POST",
                url: '{{ route("sys.role.assgs.update", ["organisation" => $organisation->id, "role" => $role->id]) }}',
                data: formData,
                success: function(d) {
                    // Hide the spinner
                    spinner.addClass('d-none');
                    submitButton.prop('disabled', false);

                    if (d.status === 2) { 
                        $("#modal").modal('hide');
                        assignDataTable.ajax.reload();
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

                    toast(1, "Error: " + xhr.status + " " + status + " " + error);
                }
            });
        });

        updatePermissionOptions();
    });
</script>