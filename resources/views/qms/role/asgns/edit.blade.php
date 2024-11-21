<style>
    #searchDiv {
        position: absolute; /* Absolute, so it's positioned based on the input field */
        width: 100%;
        background-color: var(--bs-app-header-bg);
        border: 1px solid;
        border-radius: 10px;
        max-height: 300px;
        overflow-y: auto;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        padding: 10px;
        display: none; /* Initially hidden */
    }
</style>
<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Role</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm" method="POST">
    @csrf
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">

        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Employee ID</label>
            <div class="col-sm-9">
                <input type="number" class="form-control" name="username" id="employeeId" value="{{ $user->username }}" disabled="">
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Full Name</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="name" id="fullName" value="{{ $user->name }}" disabled>
            </div>
        </div>

        <div class="mb-2 row">
            <div class="col-md-12 mb-2">
                <table id="myTable" class="table table-bordered text-center order-list">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Role Name</td>
                            <td>Delete</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user_role as $index => $selectedrole)
                        <tr>
                            <td class="col-md-1" style="padding-top: 15px">{{ $index + 1 }}</td>
                            <td class="col-md-7">
                                <select class="form-select role-select" name="role[]">
                                    <option value="" disabled>Select role</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" 
                                            @if($rol->id == $selectedrole) selected @endif>
                                            {{ $rol->role_name }} 
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="col-md-1" style="padding-top: 11px">
                                <a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        <tr id="addRow">
                            <td class="col-md-1" style="padding-top: 15px">{{ count($user_role) + 1 }}</td>
                            <td class="col-md-7">
                                <select class="form-select role-select" name="role[]">
                                    <option value="" selected disabled>Select Role</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->role_name }}</option>
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
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    $(document).ready(function () {
        var counter = 1;

        function addRow() {
            if ($("select.role-select option:not(:selected):not(:disabled)").length === 0) {
                return; // Do not add a new row if all options are selected
            }

            counter++;
            var newRow = $("<tr>");
            var cols = "";

            cols += '<td class="col-md-1" style="padding-top: 15px">' + counter + '</td>';
            cols += '<td class="col-md-8"><select class="form-select role-select" name="role[]"><option value="" selected disabled>Select role</option>@foreach($roles as $rol)<option value="{{ $rol->id }}">{{ $rol->role_name }}</option>@endforeach</select></td>';
            cols += '<td class="col-md-1" style="padding-top: 11px"><a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a></td>';

            newRow.append(cols);
            $("table.order-list").append(newRow);
            updateRoleOptions();
        }

        function updateRowNumbers() {
            $("table.order-list tbody tr").each(function(index) {
                $(this).find("td:first").text(index + 1);
                $(this).find("input[type='radio']").val(index);
            });
        }

        function updateRoleOptions() {
            var selectedrole = [];
            $("select.role-select").each(function() {
                if ($(this).val()) {
                    selectedrole.push($(this).val());
                }
            });

            $("select.role-select").each(function() {
                var currentSelect = $(this);
                currentSelect.find('option').each(function() {
                    if ($(this).val() === "") {
                        $(this).toggle(selectedrole.length < $("select.role-select option").length - 1);
                    } else if (selectedrole.includes($(this).val()) && $(this).val() !== currentSelect.val()) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });

                // Enable/disable the default role radio button based on selection
                var defaultRadio = currentSelect.closest('tr').find('.default-role');
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

            // Add a new row only if the last row has a selected role
            var lastRow = $("table.order-list tbody tr:last");
            var lastSelect = lastRow.find('.role-select');
            if (lastSelect.val() && $("select.role-select option:not(:selected):not(:disabled)").length > 0) {
                addRow();
            }
        }

        $("table.order-list").on("change", ".role-select", function () {
            updateRoleOptions();
        });

        $("table.order-list").on("click", ".deleteRow", function (event) {
            event.preventDefault();
            var row = $(this).closest("tr");
            if (!$(this).hasClass('disabled')) {
                row.remove();
                counter -= 1;
                updateRowNumbers();
                updateRoleOptions();
            }
        });


        //Add Role Form
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
            formData['username'] = $('#employeeId').val();
            formData['_token'] = '{{ csrf_token() }}';

            $.ajax({
                method: "POST",
                url: '{{ route('qms.role.asgns.update', ['organisation' => $organisation->id]) }}',
                data: formData,
                success: function(d) {
                    // Hide the spinner
                    spinner.addClass('d-none');
                    submitButton.prop('disabled', false);

                    if (d.status === 2) { 
                        $("#modal").modal('hide');
                        mainDataTable.ajax.reload();
                        toast(2, d.message);
                    } else if (d.status === 1) {
                        //let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                        toast(1, d.errors);
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

        updateRoleOptions();
    });
</script>