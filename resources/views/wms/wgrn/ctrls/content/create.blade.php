<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Scan In</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm" method="POST">
    @csrf
    <div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <div class="mb-2 row">
            <div class="col-md-2 mb-2">
                <label class="col-form-label">Serial Number</label>
                <input type="text" class="form-control" name="serial_number">
            </div>
            <div class="col-md-4 mb-2">
                <label class="col-form-label">Item Code</label>
                <input type="text" class="form-control" name="item_code">
            </div>
            <div class="col-md-3 mb-2">
                <label class="col-form-label">Manufacturer Part Number</label>
                <input type="text" class="form-control" name="mpn">
            </div>
            <div class="col-md-3 mb-2">
                <label class="col-form-label">Location</label>
                <select class="form-select" name="location" >
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" @if($location->name == "Bulk") selected @endif>{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-2 row">
            <div class="col-md-5 mb-2">
                <label class="col-form-label">Lot</label>
                <input type="text" class="form-control" name="lot">
            </div>
            <div class="col-md-3 mb-2">
                <label class="col-form-label">Manufacture Date</label>
                <input type="date" class="form-control" name="manufacture_date" required value="{{ old('manufacture_date', date('Y-m-d')) }}">
            </div>
            <div class="col-md-2 mb-2">
                <label class="col-form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity">
            </div>
            <div class="col-md-2 mb-2">
                <label class="col-form-label">UOM</label>
                <select class="form-select" name="uom">
                    <option value="" selected disabled>Select</option>
                    <option value="pcs">pcs</option>
                </select>
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
                url: '{{ route('wms.wgrn.ctrls.contents.store', ['organisation' => $organisation->id, 'header' => $header->id]) }}',
                data: formData,
                success: function(d) {
                    // Hide the spinner
                    spinner.addClass('d-none');
                    submitButton.prop('disabled', false);

                    if (d.status === 2) { 
                        $("#modal").modal('hide');
                        toast(2, d.message);

                        addRow(d.data);
                        $('#addLineForm')[0].reset();
                        $('#addLineForm input, #addLineForm select, #addLineForm textarea').removeClass('is-invalid');

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