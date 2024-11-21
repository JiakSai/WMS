<form id="mainSearchForm">
    <div class="d-flex">
        <div class="ms-auto mb-3">
            <label class="form-label">Search</label>
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control text-uppercase" name="name" placeholder="Work Order" autofocus />
                <button type="submit" class="btn btn-theme input-group-text"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->
    <table id="ipqaDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer Work Order</th>
                <th>Work Order</th>
                <th>Customer</th>
                <th>Line</th>
                <th>Modal</th>
                <th>Shift</th>
                <th>Auditor</th>
                <th>Verifier</th>
                <th>Status</th>
                <th>Template Version</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    var ipqaDataTable_data =[];
    
    $(document).ready(function() {
        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;


        $(window).trigger('resize');
        ipqaDataTable = $('#ipqaDataTable').DataTable({
            layout: {
                topStart: {
                    buttons: [{
                            extend: 'pageLength',
                            className: 'btn btn-outline-theme',

                        },
                        'spacer', 'spacer',
                        {
                            extend: 'colvis',
                            text: 'Column Filter',
                            className: 'btn btn-outline-theme',
                            autoClose: true,
                        }
                    ]
                },
                topEnd: {
                    buttons: [{
                            extend: 'csv',
                            text: '<i class="fa fa-file-excel me-1"></i> CSV',
                            className: 'btn btn-outline-theme',

                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fa fa-file-pdf me-1"></i> PDF',
                            className: 'btn btn-outline-theme',

                        }
                        ,
                        'spacer', 'spacer',
                        {
                            text: '<i class="fa fa-plus-circle me-1"></i> Add',
                            className: 'btn btn-outline-theme',
                            action: function(e, dt, node, config) {
                                $('#modal').modal('show');
                                modal('{{ route('qms.ipqa.psmts.create', ['organisation' => $organisation->id]) }}',
                                    2);
                            }
                        }
                    ]
                },
            },
            scrollY: true, //height,
            scrollX: true,
            fixedColumns: {
                left: 0,
                right: 1
            },
            destroy: true,
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            paging: true, // Enable pagination
            ajax: {
                method: "POST",
                url: '{{ route('qms.ipqa.psmts.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.name = ipqaDataTable_data.name;
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [
                { data: 'date' },
                { data: 'cus_wo' },
                { data: 'wo' },
                { data: 'customer' },
                { data: 'line' },
                { data: 'model' },
                {
                    mRender: function (data, type, row) {
                        if(row['shift'] == 'N'){
                            return 'Night';
                        } else {
                            return 'Morning';
                        } 
                    },
                },
                { data: 'card_id' },
                { data: 'verify_card_id' },
                {
                    mRender: function (data, type, row) {
                        if (row['step']  === "1") {
                            return "Pending Auditor Download";
                        } else if (row['step']  === "2"){
                            return "Pending Auditor Upload";
                        } else if (row['step']  === "3"){
                            return "Pending Verify Download";
                        } else if (row['step']  === "4"){
                            return "Pending Verify";
                        } else {
                            return "Completed";
                        }
                    }
                },
                { data: 'template_name' },
                {
                    data: null,


                    mRender: function(data, type, row) {
                        
                        var id = row['id'];
                        $shift = row['shift'];
                        $file_name = row['file_name'];
                        if ($shift === "M") {
                            upload_num = 3;
                        } else {
                            upload_num = 2;
                        }
                        $n = $m ='';
                        if((row['step']  === "1")) 
                        {
                            $n = '<li><a href="#" class="dropdown-item" onClick="btnDownload(' +  id + ')"><i class="bi bi-download"></i> Download IPQA Checklist</a></li>';
                        }
                        else if((row['step']  === "2") ) 
                        {
                            $n = '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('qms.ipqa.psmts.getStep2Modal', ['organisation' => $organisation->id]) }}' + '\', \'' + id + '\')"><i class="bi bi-pencil-square"></i> Upload Form</a></li>';
                            $n += '<li><a href="#" class="dropdown-item" onClick="btnDownload(' +  id + ')"><i class="bi bi-download"></i> Download IPQA Checklist</a></li>';
                        }
                        else if((row['step']  === "3"))
                        {
                            $n = '<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('qms.ipqa.psmts.getStep2Modal', ['organisation' => $organisation->id]) }}' + '\', \'' + id + '\')"><i class="bi bi-pencil-square"></i> Reupload Template</a></li>';
                            $n += '<li><a href="#" class="dropdown-item" onClick="btnVerifyDownload(' +  id + ')"><i class="bi bi-download"></i> Download IPQA Checklist</a></li>'; 
                        }
                        else if((row['step']  === "4"))
                        {
                            $n += '<li><a href="#" class="dropdown-item" onClick="completeVerify(' +  id + ')"><i class="bi bi-download"></i> Complete Verify</a></li>';
                            $n += '<li><a href="#" class="dropdown-item" onClick="btnVerifyDownload(' +  id + ')"><i class="bi bi-download"></i> Download IPQA Checklist</a></li>'
                        } else {
                            $n += '<li><a href="#" class="dropdown-item" onClick="downloadCompleteChecksheet(' +  id + ')"><i class="bi bi-download"></i> Download IPQA Checklist</a></li>'; 
                        }

                        
                        var isActive = row['is_active'] === 'Active';
                        var actionText = isActive ? 'Deactivate' : 'Activate';
                        var actionIcon = isActive ? 'bi bi-person-fill-x' : 'bi bi-person-check-fill';
                        var actionFunction = isActive ? 'btnDeactivate' : 'btnActivate';

                        return '<center><div class="dropdown">'    
                        +'<button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</button>'   
                        +'<ul class="dropdown-menu z-1">' 
                        + $n
                        +'</ul>'    
                        +'</div></center>'  
                    },
                },
            ],
        });
        $(window).trigger('resize');
    });

    function handleClick(button) {
        // Select all td elements and reset their z-index to the default value
        const allTdElements = document.querySelectorAll("td");
        allTdElements.forEach(td => {
            td.style.zIndex = ""; // Resets to the default value
        });

        // Find the closest td element to the clicked button and set its z-index to 200
        const tdElement = button.closest("td");
        if (tdElement) {
            tdElement.style.zIndex = 200;
        }
    }

    function btnDownload(id) {
        $.ajax({
            method: "POST",
            url: '{{ route('qms.ipqa.psmts.stepOne', ['organisation' => $organisation->id]) }}',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            xhrFields: {
                responseType: 'blob'  // Ensures binary file handling for successful response
            },
            success: function(d, status, xhr)  {
                if (d.status === 1) {
                    try{
                        let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                        toast(1, errorMessages);                            
                    } catch (e){
                        toast(1, d.message);
                    }
                } else {
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    var blob = new Blob([d], { type: xhr.getResponseHeader('Content-Type') });
                    var filename = '';

                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        var matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    // Initiate file download
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename || 'downloaded_file';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    toast(2, "Download Successfully");
                    ipqaDataTable.ajax.reload();  // Reload the table data
                }
            },
            error: function(xhr, status, error) {

                toast(1,"Error: No permission to download");
            }
        });
    }

    function completeVerify(id){
        $.ajax({
            method: "POST",
            url: '{{ route('qms.ipqa.psmts.stepFour', ['organisation' => $organisation->id]) }}',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(d) {
                if (d.status === 2) { 
                    $("#modal").modal('hide');
                    ipqaDataTable.ajax.reload();
                    toast(2, d.data);
                } else if (d.status === 1) {
                    try{
                        let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                        toast(1, errorMessages);                            
                    } catch (e){
                        toast(1, d.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                toast(1,"Error:", xhr.status, status, error);
            }
        });
    }
    function btnVerifyDownload(id) {
        $.ajax({
            method: "POST",
            url: '{{ route('qms.ipqa.psmts.stepThree', ['organisation' => $organisation->id]) }}',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            xhrFields: {
                responseType: 'blob'  // Expecting a file (binary data)
            },
            success: function (data, status, xhr) {
                var filename = ""; 
                var disposition = xhr.getResponseHeader('Content-Disposition');

                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }

                // Create a download link for the blob object
                var blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename || 'downloaded_file';  // Set filename or fallback to 'downloaded_file'
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                toast(2, "Download Successfully");
                ipqaDataTable.ajax.reload();  // Reload the table data
            },
            error: function (xhr, status, error) {
                toast(1,"Error: No permission to download");
            }
        });
    }
    $("#mainSearchForm").submit(function(event) {
        
        event.preventDefault();
        
        // Serialize the form data
        let searchFormData = $(this).serialize();

        ipqaDataTable_data.name = $(this).find('[name="name"]').val();
    
        ipqaDataTable.ajax.reload();
    });
    function downloadCompleteChecksheet(id){
        $.ajax({
            method: "POST",
            url: '{{ route('qms.ipqa.psmts.stepFive', ['organisation' => $organisation->id]) }}',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            xhrFields: {
                responseType: 'blob'  // Expecting a file (binary data)
            },
            success: function (data, status, xhr) {
                var filename = ""; 
                var disposition = xhr.getResponseHeader('Content-Disposition');

                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }
                }

                // Create a download link for the blob object
                var blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename || 'downloaded_file';  // Set filename or fallback to 'downloaded_file'
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                toast(2, "Download Successfully");
                ipqaDataTable.ajax.reload();  // Reload the table data
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }
</script>
