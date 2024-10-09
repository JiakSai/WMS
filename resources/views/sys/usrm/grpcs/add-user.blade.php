<style>
    #myUL {
        border: var(--bs-border-width) solid var(--bs-border-color);
        padding: 0;
        margin: 0;
        height: 200px;
        overflow-y: auto;
    }

    #myUL li a {
        border: var(--bs-border-width) solid var(--bs-border-color);
        margin-top: -1px; /* Prevent double borders */
        padding: 12px;
        color: var(--bs-body-color);
        text-decoration: none;
        display: block;
    }
</style>

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Add User to {{ $group->name }}</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body">
        <div class="mb-3 row">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Group Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" value="{{ $group->name }}" readonly>
                    <input type="hidden" name="id" value="{{ $group->id }}">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Search</label>
                <div class="flex-nowrap col-sm-9" style="position: relative; display: flex; flex-wrap: wrap; align-items: stretch;">
                    <input type="text" class="form-control" name="search_user" id="search-user" placeholder="USER ID / USERNAME" autofocus="">
                    <button type="button" class="btn btn-theme input-group-text" id="search-button"><i class="bi bi-search"></i></button>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Select User</label>
                <div class="col-sm-9">
                    <ul id="myUL">
                        @foreach($usersNotInGroup as $user)
                        <li><a href="#" class="user-link" data-user-id="{{ $user->id }}">{{ $user->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Selected User</label>
                <div class="col-sm-9" id="selected-user">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary">Add User
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    $('#search-button').on('click', function() {
        var searchTerm = $('#search-user').val().toLowerCase();

        $('#myUL li').each(function() {
            var optionText = $(this).text().toLowerCase();

            // Use a regular expression to perform a "like" match
            var regex = new RegExp(searchTerm, 'i'); // 'i' makes it case-insensitive
            if (regex.test(optionText)) {
                $(this).removeClass('d-none').show();
            } else {
                $(this).addClass('d-none').hide();
            }
        });
    });

    $('#myUL').on('click', '.user-link', function(e) {
        e.preventDefault(); // Prevent the default action of the hyperlink

        var userId = $(this).data('user-id');
        var userName = $(this).text();

        // Check if the button already exists
        if ($('#selected-user button[value="' + userId + '"]').length > 0) {
            toast(1, "User already selected");
        } else {
            // Create a new button element
            var buttonHtml = `
                <button class="btn btn-theme remove-btn" value="${userId}">
                    ${userName} <span class="ms-2">&times;</span>
                </button>
            `;

            // Append the button to the selected user div
            $('#selected-user').append(buttonHtml);
        }
    });

    // Event delegation to handle removing buttons
    $('#selected-user').on('click', '.remove-btn', function() {
        $(this).remove();
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

        // Collect selected user IDs
        let selectedUsers = [];
        $('#selected-user button').each(function() {
            selectedUsers.push($(this).val());
        });

        formData['users'] = selectedUsers;
        formData['_token'] = '{{ csrf_token() }}';

        $.ajax({
            method: "POST",
            url: '{{ route('sys.usrm.grpcs.store-user', ['organisation' => $organisation->id]) }}',
            data: formData,
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 
                    $("#modal").modal('hide');
                    groupDataTable.ajax.reload();
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
</script>