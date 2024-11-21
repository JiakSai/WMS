<style>
    .custom-search-div {
        position: absolute; /* Absolute, so it's positioned based on the input field */
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

<link href="{{ asset('css/multiSelect.css') }}" rel="stylesheet">

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Group Level</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm" method="POST">
    @csrf
    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Group Name</label>
            <div class="col-sm-9">
                <input type="text" id="group_name" name="group" class="form-control" value="{{ $groups->name }}" disabled>
            </div>
        </div>
        <input type="hidden" id="group_id" name="group_id" class="form-control" value="{{ $groups->id }}">

        @foreach($level as $index => $lvl)
            <div class="row mb-3">
                <div class="form-group">
                    <label for="userSelect_{{ $loop->index }}">{{ $lvl->level }} ({{ $lvl->descriptions }})</label>
                    <select id="userSelect_{{ $loop->index }}" name="userSelect_{{ $loop->index }}" class="form-control user-multi-select" multiple data-max="{{ $lvl->max_users }}">
                    </select>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script src="{{ asset('js/multiSelect.js') }}"></script>
<script>
    var userData = @json($userData);
    var selectedUsers = @json($selected_users_by_level);
    $(document).ready(function () {
        document.querySelectorAll('.user-multi-select').forEach(function (selectElement, index) {
            // Clear any existing options
            selectElement.innerHTML = '';
            // Determine the current level_id based on the index
            const levelId = index + 1; // Assuming the first element is for level 1, second for level 2, etc.
            var selectedUserss = selectedUsers[levelId] || []; // Get selected users for the current level
            // Populate the select element with options
            userData.forEach(user => {
                const option = document.createElement('option');
                option.value = user.username;
                option.text = `${user.username}-${user.name}`;
                selectedUserss.forEach(selectedUser => {
                    if (selectedUser == user.username) {
                        option.selected = true;
                    }
                });
                selectElement.appendChild(option);
            });

            // Initialize MultiSelect after adding options
            new MultiSelect(selectElement, {
                placeholder: 'Select Users',
                search: true,
                selectAll: false,
                max: parseInt(selectElement.getAttribute('data-max'), 10) || 2
            });
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


            formData['_token'] = '{{ csrf_token() }}';

            $.ajax({
                method: "POST",
                url: '{{ route('mrb.lvlc.galls.update', ['organisation' => $organisation->id]) }}',
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

    });
</script>