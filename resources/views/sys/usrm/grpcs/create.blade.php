<link href="{{ asset('css/multiSelect.css') }}" rel="stylesheet">

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Add User Group</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Group Name</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name">
                </div>
            </div>
            <label for="dynamic">Dynamic Select</label>
            <select id="dynamic" name="dynamic"></select>
        </div>
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary" id="submitButton">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script src="{{ asset('js/multiSelect.js') }}"></script>

<script>
    new MultiSelect('#dynamic', {
        data: [
            {
                value: 'opt1',
                text: 'Option 1'
            },
            {
                value: 'opt2',
                html: '<strong>Option 2 with HTML!</strong>'
            },
            {
                value: 'opt3',
                text: 'Option 3',
                selected: true
            },
            {
                value: 'opt4',
                text: 'Option 4'
            },
            {
                value: 'opt5',
                text: 'Option 5'
            }
        ],
        placeholder: 'Select an option',
        search: true,
        selectAll: false,
        listAll: false,
        max: 2,
        onChange: function(value, text, element) {
            console.log('Change:', value, text, element);
        },
        onSelect: function(value, text, element) {
            console.log('Selected:', value, text, element);
        },
        onUnselect: function(value, text, element) {
            console.log('Unselected:', value, text, element);
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
            url: '{{ route('sys.usrm.grpcs.store', ['organisation' => $organisation->id]) }}',
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

                toast(1,"Error:", xhr.status, status, error);
            }
        });
    });
</script>