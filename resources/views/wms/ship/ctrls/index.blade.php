<form id="shipmentSearchForm">
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
    <table id="shipmentDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                <th>Shipment No</th>
                <th>Warehouse</th>
                <th>Customs Slip</th>
                <th>Shipment Slip</th>
                <th>Invoice</th>
                <th>Shipment Date</th>
                <th>Status</th>
                <th>Approve By</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    var shipmentDataTable_data =[];
    
    $(document).ready(function() {

        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;

        $(window).trigger('resize');
        shipmentDataTable = $('#shipmentDataTable').DataTable({
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
                                window.location.href = '{{ route('wms.ship.ctrls.create', ['organisation' => $organisation->id]) }}';
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
                url: '{{ route('wms.ship.ctrls.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.name = shipmentDataTable_data.name;
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [{
                    data: 'shipment_no'
                },
                {
                    data: 'warehouse'
                },
                {
                    data: 'customs_slip'
                },
                {
                    data: 'shipment_slip'
                },
                {
                    data: 'invoice'
                },
                {
                    data: 'shipment_date'
                },
                {
                    data: 'status'
                },
                {
                    data: 'updated_by'
                },
                {
                    data: null,


                    mRender: function(data, type, row) {
                        var shipment_no = row['shipment_no'];

                        return '<center><div class="dropdown">'    
                        +'<button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</button>'   
                        +'<ul class="dropdown-menu">'
                        +'<li><a href="#" class="dropdown-item" onClick="btnView(\'' + shipment_no +'\')"><i class="bi bi-eye"></i> View</a></li>'
                        +'<li><a href="#" class="dropdown-item" onClick="btnApprove(\'' + shipment_no +'\')"><i class="bi bi-check2-square"></i> Approve</a></li>'
                        +'<li><a href="#" class="dropdown-item" onClick="btnRemove(\'' + shipment_no +'\')"><i class="bi bi-trash-fill"></i> Delete</a></li>'
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

    function btnRemove(shipment_no) {
        $.ajax({
            type: "DELETE",
            url: '{{ route('wms.ship.ctrls.destroy', ['organisation' => $organisation->id]) }}',
            dataType: 'json',
            data: { 
                shipment_no: shipment_no,
                _token: '{{ csrf_token() }}' // Include the CSRF token
            },
            success: function (d) {
                if (d.status === 2) {
                    toast(2, d.message);
                } else {
                    toast(1, d.message);
                }
                shipmentDataTable.ajax.reload();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

    function btnApprove(shipment_no) {
        $.ajax({
            type: "POST",
            url: '{{ route('wms.ship.ctrls.approve', ['organisation' => $organisation->id]) }}',
            dataType: 'json',
            data: { 
                shipment_no: shipment_no,
                _token: '{{ csrf_token() }}' // Include the CSRF token
            },
            success: function (d) {
                if (d.status === 2) {
                    toast(2, d.message);
                } else {
                    toast(1, d.message);
                }
                shipmentDataTable.ajax.reload();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

    // Function to handle View Button Click
    function btnView(shipmentNo) {
        window.location.href = "{{ route('wms.ship.ctrls.view', ['organisation' => $organisation->id, 'shipment' => 'SHIPMENT_NO_PLACEHOLDER']) }}".replace('SHIPMENT_NO_PLACEHOLDER', shipmentNo);
    }

    $("#shipmentSearchForm").submit(function(event) {
        
        event.preventDefault();
        
        // Serialize the form data
        let searchFormData = $(this).serialize();

        shipmentDataTable_data.name = $(this).find('[name="name"]').val();
    
        shipmentDataTable.ajax.reload();
    });
    
</script>

