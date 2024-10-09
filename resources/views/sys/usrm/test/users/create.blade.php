<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Add User</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm" method="POST">
    @csrf
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="mb-2 row">
            <div class="col-md-2 mb-2">
                <label class="col-form-label">Employee ID</label>
                <input type="number" class="form-control" name="username" value="{{ $username }}" {{ $readOnly }}>
            </div>
            <div class="col-md-5 mb-2">
                <label class="col-form-label">Full Name</label>
                <input type="text" class="form-control" name="name" value="{{ $name }}">
            </div>
            <div class="col-md-5 mb-2">
                <label class="col-form-label">Group</label>
                <select class="form-select" name="group">
                    <option value="" selected disabled>Select Group</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-md-7 mb-2">
                <label class="col-form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ $email }}">
            </div>
            <div class="col-md-3 mb-2">
                <label class="col-form-label">Phone Number</label>
                <input type="number" class="form-control" name="phone_number" value="{{ $phone_number }}">
            </div>
            <div class="col-md-2 mb-2">
                <label class="col-form-label">Telegram ID</label>
                <input type="text" class="form-control" name="telegram_id" value="{{ $telegram_id }}">
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-md-12 mb-2">
                <table id="myTable" class="table table-bordered text-center order-list">
                    <thead>
                        <tr>
                            <td>#</td>
                            <td>Warehouse</td>
                            <td>Default</td>
                            <td>Delete</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-md-1" style="padding-top: 15px">1</td>
                            <td class="col-md-7">
                                <select class="form-select warehouse-select" name="warehouse[]">
                                    <option value="" selected disabled>Select Warehouse</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="col-md-1"style="padding-top: 13px">
                                <input type="radio" class="form-check-input default-warehouse" name="default_warehouse" value="0">
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
            if ($("select.warehouse-select option:not(:selected):not(:disabled)").length === 0) {
                return; // Do not add a new row if all options are selected
            }

            counter++;
            var newRow = $("<tr>");
            var cols = "";

            cols += '<td class="col-md-1" style="padding-top: 15px">' + counter + '</td>';
            cols += '<td class="col-md-7"><select class="form-select warehouse-select" name="warehouse[]"><option value="" selected disabled>Select Warehouse</option>@foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach</select></td>';
            cols += '<td class="col-md-1" style="padding-top: 15px"><input type="radio" class="form-check-input default-warehouse" name="default_warehouse" value="' + (counter - 1) + '" disabled></td>';
            cols += '<td class="col-md-1" style="padding-top: 11px"><a href="#" class="btn btn-dark btn-sm me-1 deleteRow"><i class="bi bi-trash"></i></a></td>';

            newRow.append(cols);
            $("table.order-list").append(newRow);
            updateWarehouseOptions();
        }

        function updateRowNumbers() {
            $("table.order-list tbody tr").each(function(index) {
                $(this).find("td:first").text(index + 1);
                $(this).find("input[type='radio']").val(index);
            });
        }

        function updateWarehouseOptions() {
            var selectedWarehouses = [];
            $("select.warehouse-select").each(function() {
                if ($(this).val()) {
                    selectedWarehouses.push($(this).val());
                }
            });

            $("select.warehouse-select").each(function() {
                var currentSelect = $(this);
                currentSelect.find('option').each(function() {
                    if ($(this).val() === "") {
                        $(this).toggle(selectedWarehouses.length < $("select.warehouse-select option").length - 1);
                    } else if (selectedWarehouses.includes($(this).val()) && $(this).val() !== currentSelect.val()) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });

                // Enable/disable the default warehouse radio button based on selection
                var defaultRadio = currentSelect.closest('tr').find('.default-warehouse');
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

            // Add a new row only if the last row has a selected warehouse
            var lastRow = $("table.order-list tbody tr:last");
            var lastSelect = lastRow.find('.warehouse-select');
            if (lastSelect.val() && $("select.warehouse-select option:not(:selected):not(:disabled)").length > 0) {
                addRow();
            }
        }

        $("table.order-list").on("change", ".warehouse-select", function () {
            updateWarehouseOptions();
        });

        $("table.order-list").on("click", ".deleteRow", function (event) {
            event.preventDefault();
            var row = $(this).closest("tr");
            if (!$(this).hasClass('disabled')) {
                row.remove();
                counter -= 1;
                updateRowNumbers();
                updateWarehouseOptions();
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
                url: '{{ route('addUserSubmit', ['warehouse' => $warehouse->id]) }}',
                data: formData,
                success: function(d) {
                    // Hide the spinner
                    spinner.addClass('d-none');
                    submitButton.prop('disabled', false);

                    if (d.status === 2) { 
                        $("#modal").modal('hide');
                        userDatatable.ajax.reload();
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

        updateWarehouseOptions();
    });
</script>