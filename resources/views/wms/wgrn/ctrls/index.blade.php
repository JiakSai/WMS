<form id="grnSearchForm">
    <div class="d-flex">
        <div class="ms-auto mb-3">
            <label class="form-label">Search</label>
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control text-uppercase" name="name" placeholder="GRN ID" autofocus />
                <button type="submit" class="btn btn-theme input-group-text"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->
    <table id="grnDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                <th>Receipt</th>
                <th>Warehouse</th>
                <th>Packing Slip</th>
                <th>DO</th>
                <th>Invoice</th>
                <th>Receipt Date</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    var grnDataTable_data =[];
    
    $(document).ready(function() {

        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;

        $(window).trigger('resize');
        grnDataTable = $('#grnDataTable').DataTable({
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

                        },
                        'spacer', 'spacer',
                        {
                            text: '<i class="fa fa-plus-circle me-1"></i> Add',
                            className: 'btn btn-outline-theme',
                            action: function(e, dt, node, config) {
                                window.location.href = '{{ route('wms.wgrn.ctrls.create', ['organisation' => $organisation->id]) }}';
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
                url: '{{ route('wms.wgrn.ctrls.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.name = grnDataTable_data.name;
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [{
                    data: 'receipt'
                },
                {
                    data: 'warehouse'
                },
                {
                    data: 'packing_slip'
                },
                {
                    data: 'do'
                },
                {
                    data: 'invoice'
                },
                {
                    data: 'receipt_date'
                },
                {
                    data: 'created_by'
                },
                {
                    data: 'updated_by'
                },
                {
                    data: null,


                    mRender: function(data, type, row) {
                        var receipt = row['receipt'];

                        return '<center><div class="dropdown">'    
                        +'<button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</button>'   
                        +'<ul class="dropdown-menu">'
                        +'<li><a href="#" class="dropdown-item" onClick="btnView(\'' + receipt +'\')"><i class="bi bi-eye"></i> View</a></li>'
                        +'<li><a href="#" class="dropdown-item" onClick="btnRemove(\'' +  receipt +'\')"><i class="bi bi-trash-fill"></i> Delete</a></li>'
                        +'</ul>'    
                        +'</div></center>'  
                    },
                },
            ],
        });

        $(window).trigger('resize');
    });

    function handleClick(button) {
        // Find the closest td element and set its z-index to 200
        const tdElement = button.closest("td");
        if (tdElement) {
            tdElement.style.zIndex = 1050;
            console.log("z-index set to 200 for:", tdElement);
        }
    }

    function btnView(receipt) {
        window.location.href = "{{ route('wms.wgrn.ctrls.view', ['organisation' => $organisation->id, 'header' => 'SHIPMENT_NO_PLACEHOLDER']) }}".replace('SHIPMENT_NO_PLACEHOLDER', receipt);
    }

    function btnRemove(receipt) {
        $.ajax({
            type: "DELETE",
            url: '{{ route('wms.wgrn.ctrls.destroy', ['organisation' => $organisation->id]) }}',
            dataType: 'json',
            data: { 
                receipt: receipt,
                _token: '{{ csrf_token() }}' // Include the CSRF token
            },
            success: function (d) {
                if (d.status === 2) {
                    toast(2, d.message);
                } else {
                    toast(1, d.message);
                }
                grnDataTable.ajax.reload();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

    $("#grnSearchForm").submit(function(event) {
        
        event.preventDefault();
        
        // Serialize the form data
        let searchFormData = $(this).serialize();

        grnDataTable_data.name = $(this).find('[name="name"]').val();
    
        grnDataTable.ajax.reload();
    });
    
</script>

