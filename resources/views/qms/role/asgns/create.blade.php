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
    <h1 class="modal-title fs-5" id="exampleModalLabel">Add QMS Role to User</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm" method="POST">
    @csrf
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Enter Employee ID</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="searchInput" placeholder="Type to search...">
                <div id="searchDiv">
                    <span class="spinner-border spinner-border-sm d-none spinner-border-search" role="status" aria-hidden="true"></span>
                    <div id="searchResults">
                        <span class="spinner-border spinner-border-sm d-none spinner-border-search" role="status" aria-hidden="true"></span>
                    </div>    
                </div>             
            </div>
        </div>

        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Selected Employee</label>
            <div class="col-sm-9">
                <input type="number" class="form-control" name="username" id="employeeId" disabled="">
            </div>
        </div>
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Full Name</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="name" id="fullName" disabled>
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
                        <tr>
                            <td class="col-md-1" style="padding-top: 15px">1</td>
                            <td class="col-md-8">
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

        // START OF SEARCH USER
        $(document).on('click', '#searchDiv a', function(e) {
            e.preventDefault(); // Prevent the default anchor behavior

            // Get the selected employee's ID and name from the clicked link
            const selectedUserId = $(this).data('user_id');
            const selectedUserName = $(this).data('name');

            // Populate the selected employee ID and full name in the respective input fields
            $('#employeeId').val(selectedUserId);
            $('#fullName').val(selectedUserName);

            // Hide the search results after selection
            $('#searchDiv').fadeOut();
        });
        
        // Function to update the position of the search results container
        function updateSearchResultsPosition() {
            var inputOffset = $('#searchInput').offset();
            var inputHeight = $('#searchInput').outerHeight();
            $('#searchDiv').css({
            width: $('#searchInput').outerWidth() // same width as the input field
            });
        }

        // Handle input event to show/hide search results
        $('#searchInput').on('input', function() {
            var query = $(this).val().trim();
            let spinner = $('#searchDiv').find('.spinner-border-search');
            
            // Clear previous results but keep the spinner visible
            $('#searchResults').empty(); // Clear previous results (if needed)
        
            if (query.length > 0) {
                spinner.removeClass('d-none'); // Show spinner

                $.ajax({
                    method: "POST",
                    url: '{{ route('qms.role.asgns.getUsersWithoutRole', ['organisation' => $organisation->id]) }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "name": query
                    },
                    success: function(d) {
                        $('#searchResults').empty(); // Clear previous results (if needed)
                        // Clear previous results after spinner is shown
                        spinner.addClass('d-none'); // Hide spinner after getting response
                        
                        if (d.status === 2) { 
                            // Display the results as links
                            d.data.forEach(function(result) {
                                $('#searchResults').append(`<a href="#" data-user_id="${result.username}" data-name="${result.name}">${result.username} - ${result.name}</a></br>`);
                            });
                        } else if (d.status === 1) {
                            $('#searchResults').html(`No User Found`);
                        }
                    },
                    error: function(xhr, status, error) {
                        spinner.addClass('d-none'); // Hide spinner on error
                        toast(1, "Error:", xhr.status, status, error);
                    }
                });
                
                // Show the search results container
                $('#searchDiv').fadeIn();
            } else {
                // Hide the search results if the input is empty
                $('#searchDiv').fadeOut();
                spinner.addClass('d-none'); // Hide spinner if no query
            }
        });


        // Hide the results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#searchInput, #searchResults').length) {
            $('#searchDiv').fadeOut();
            }
        });

        // Handle window resize to reposition the search results
        $(window).on('resize', function() {
            if ($('#searchDiv').is(':visible')) {
            updateSearchResultsPosition(); // Reposition the container when window resizes
            }
        });

        // END OF SEARCH USER

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
                url: '{{ route('qms.role.asgns.store', ['organisation' => $organisation->id]) }}',
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

        updateRoleOptions();
    });
</script>