<div class="modal-header">
    <h1 class="modal-title fs-5" id="modalTitle">
        @if(isset($role->id))
            Edit Role
        @else
            Add Role
        @endif
    </h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="roleForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Role Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" value="{{ old('name', $role->name ?? '') }}">
                    <input type="hidden" class="form-control" name="mainModule" value="{{ $mainModule->id }}">
                    @if(isset($role->id))
                        <input type="hidden" name="roleId" value="{{ $role->id }}">
                    @endif
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-md-12 mb-2">
                    <table id="userTable" class="table table-bordered text-center user-order-list">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>User</td>
                                <td>Delete</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($selectedUsers))
                                @foreach($selectedUsers as $selectedUser)
                                    @php $userCounter++; @endphp
                                    <tr>
                                        <td class="col-md-1" style="padding-top: 15px">{{ $userCounter }}</td>
                                        <td class="col-md-7">
                                            <select class="form-select user-select" name="user[]">
                                                <option value="" selected disabled>Select User</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ $user->id == $selectedUser ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="col-md-1" style="padding-top: 11px">
                                            <a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <!-- If no selected users, display one empty row -->
                                <tr>
                                    <td class="col-md-1" style="padding-top: 15px">1</td>
                                    <td class="col-md-7">
                                        <select class="form-select user-select" name="user[]">
                                            <option value="" selected disabled>Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
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
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary" id="submitButton">
            @if(isset($role->id))
                Update
            @else
                Submit
            @endif
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    $(document).ready(function () {
        var userCounter = $("table.user-order-list tbody tr").length || 1;

        function addUserRow() {
            if ($("select.user-select option:not(:selected):not(:disabled)").length === 0) {
                return; // Do not add a new row if all options are selected
            }

            userCounter++;
            var newRow = $("<tr>");
            var cols = "";

            cols += '<td class="col-md-1" style="padding-top: 15px">' + userCounter + '</td>';
            cols += '<td class="col-md-7"><select class="form-select user-select" name="user[]"><option value="" selected disabled>Select User</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></td>';
            cols += '<td class="col-md-1" style="padding-top: 11px"><a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a></td>';

            newRow.append(cols);
            $("table.user-order-list").append(newRow);
            updateUserOptions();
        }

        function updateUserRowNumbers() {
            $("table.user-order-list tbody tr").each(function(index) {
                $(this).find("td:first").text(index + 1);
            });
        }

        function updateUserOptions() {
            var selectedUsers = [];
            $("select.user-select").each(function() {
                if ($(this).val()) {
                    selectedUsers.push($(this).val());
                }
            });

            $("select.user-select").each(function() {
                var currentSelect = $(this);
                currentSelect.find('option').each(function() {
                    if ($(this).val() === "") {
                        $(this).toggle(selectedUsers.length < $("select.user-select option").length - 1);
                    } else if (selectedUsers.includes($(this).val()) && $(this).val() !== currentSelect.val()) {
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

            // Add a new row only if the last row has a selected user
            var lastRow = $("table.user-order-list tbody tr:last");
            var lastSelect = lastRow.find('.user-select');
            if (lastSelect.val() && $("select.user-select option:not(:selected):not(:disabled)").length > 0) {
                addUserRow();
            }
        }

        $("table.user-order-list").on("change", ".user-select", function () {
            updateUserOptions();
        });

        $("table.user-order-list").on("click", ".deleteRow", function (event) {
            event.preventDefault();
            var row = $(this).closest("tr");
            if (!$(this).hasClass('disabled')) {
                row.remove();
                userCounter -= 1;
                updateUserRowNumbers();
                updateUserOptions();
            }
        });

        $("#roleForm").submit(function(event) {
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

            // Determine if we're editing or creating
            let isEdit = $('input[name="roleId"]').length > 0;
            let url = '';
            let method = '';
            if (isEdit) {
                url = '{{ route('rbp.role.update', ['organisation' => $organisation->id, 'roleId' => ':roleId']) }}'.replace(':roleId', formData['roleId']);
                method = 'PUT';
            } else {
                url = '{{ route('rbp.role.store', ['organisation' => $organisation->id]) }}';
                method = 'POST';
            }

            $.ajax({
                method: method,
                url: url,
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

                    toast(1, "Error: " + error);
                }
            });
        });

        updateUserOptions();
    });
</script>