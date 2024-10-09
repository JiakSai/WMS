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
      display: block
    }
    

    </style>
<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Add User to {{ $name }}</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body">
        <div class="mb-3 row">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Warehouse Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" value="{{ $name }}" id="warehouse_name" readonly>
                    <input type="hidden" name="id" value="{{ $id }}">
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
                        @foreach($usersNotInWarehouse as $user)
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
        <button type="button" class="btn btn-primary" id="add_user_btn">Add User
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
        if ($('#selected-user button[data-user-id="' + userId + '"]').length > 0) {
            toast(1, "User already selected");
        } else {
            // Create a new button element
            var buttonHtml = `
                <button class="btn btn-theme remove-btn" data-user-id="${userId}">
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
    $('#add_user_btn').on('click', function() {
        // Collect all the IDs and values from the generated buttons
        var selectedUsers = [];
        var warehouse_name = $('#warehouse_name').val();
        $('#selected-user button').each(function() {
            var userId = $(this).data('user-id');
            selectedUsers.push({ id: userId});
        });
        // if (empty(selecetedUsers)){
        //     toast(1,"No User Selected");
        // }
        // Example: Log the selected users to the console
        $.ajax({
            method: "POST",
            url: '{{ route('addUserToWarehouseSubmit') }}',
            data: {
                name: warehouse_name,        // Pass the warehouse ID
                users: selectedUsers,          // Pass the selected user IDs
                _token: '{{ csrf_token() }}'
            },
            success: function(d) {
                if (d.status === 2) { 
                        $("#modal").modal('hide');
                        datatable.ajax.reload();
                        toast(2, d.message);
                    } else if (d.status === 1) {
                        let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                        toast(1, errorMessages);
                    }
            },
            error: function(xhr, status, error) {
                toast(1,"Error:", xhr.status, status, error);
            }
        });
    });
    // $("#addForm").submit(function(event) {
    //     // Prevent the default form submission
    //     event.preventDefault();
    //     let formDataArray = $(this).serializeArray();
    //     let formData = {};
    //     formDataArray.forEach(function(item) {
    //         formData[item.name] = item.value;
    //     });
    //     formData['_token'] = '{{ csrf_token() }}';

    //     $.ajax({
    //         method: "POST",
    //         url: '{{ route('addWarehouseSubmit') }}',
    //         data: formData,
    //         success: function(d) {
    //             if (d.status === 2) { 
    //                     $("#modal").modal('hide');
    //                     datatable.ajax.reload();
    //                     toast(2, d.message);
    //                 } else if (d.status === 1) {
    //                     let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
    //                     toast(1, errorMessages);
    //                 }
    //         },
    //         error: function(xhr, status, error) {
    //             toast(1,"Error:", xhr.status, status, error);
    //         }
    //     });
    // });
</script>