<link href="{{asset('css/bootstrapicons-iconpicker.css')}}"  rel="stylesheet">
<script src="{{asset('js/bootstrapicon-iconpicker.min.js')}}"></script>

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Print RFID</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body">
        <div class="mb-3 row">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Quantity</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" name="quantity" value="{{ $quantity}}">
                    <input type="hidden" name="created_by" value="{{ $created_by }}">
                    <input type="hidden" name="updated_by" value="{{ $updated_by }}">
                </div>
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

<script src="{{asset('js/multiSelect.js')}}"></script>

<script>

    $("#addForm").submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();
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

        $('#modalContent').html('<div class="text-center"><div class="spinner-border" style="width: 5rem; height: 5rem;" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        $.ajax({
            method: "POST",
            url: '{{ route('printRfidSubmit', ['warehouse' => $warehouse->id]) }}',
            data: formData,
            success: function(d) {
                if (d.status === 2) { 
                        $("#modal").modal('hide');
                        datatable.ajax.reload();
                        toast(2, d.message);

                        // Open new window and print QR codes
                        let printWindow = window.open('', 'PrintWindow', 'width=800,height=600');
                        let printContent = '<html><head><title>RFID QR Codes</title>';
                        printContent += '<style>body { font-family: Arial, sans-serif; } .qr-container { margin: 10px 0 } .qr-code { width: 64px; height: 64px; } .rfid-code { margin-top: 5px; font-size: 12px; }</style>';
                        printContent += '</head><body>';

                        d.qrCodes.forEach((qrCode, index) => {
                            printContent += '<div class="qr-container">';
                            printContent += '<img class="qr-code" style="padding-left:25px" src="data:image/svg+xml;base64,' + qrCode + '" />';
                            printContent += '<div class="rfid-code">' + d.rfidCodes[index] + '</div>';
                            printContent += '</div>';
                        });

                        printContent += '<script>window.onload = function() { window.print(); window.onafterprint = function() { window.close(); } }<\/script>';
                        printContent += '</body></html>';

                        printWindow.document.open();
                        printWindow.document.write(printContent);
                        printWindow.document.close();

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
</script>