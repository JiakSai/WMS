<form id="invtSearchForm">
    <div class="d-flex">
        <div class="ms-auto mb-3">
            <label class="form-label">Search</label>
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control text-uppercase" name="name" placeholder="Item Name" autofocus />
                <button type="submit" class="btn btn-theme input-group-text"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->
    <table id="invtDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                <th>Item Code</th>
                <th>Warehouse</th>
                <th>Warehouse Location</th>
                <th>Lot</th>
                <th>Quantity</th>
                <th>UOM</th>
                <th>Manufacture Date</th>
                <th>Created By</th>
                <th>Updated By</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    var invtDataTable_data =[];
    
    $(document).ready(function() {

        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;

        $(window).trigger('resize');
        invtDataTable = $('#invtDataTable').DataTable({
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
                    ]
                },
            },
            scrollY: true, //height,
            scrollX: true,
            destroy: true,
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            paging: true, // Enable pagination
            ajax: {
                method: "POST",
                url: '{{ route('wms.invt.ctrls.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.name = invtDataTable_data.name;
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [{
                    data: 'item_code'
                },
                {
                    data: 'warehouse'
                },
                {
                    data: 'warehouse_location'
                },
                {
                    data: 'lot'
                },
                {
                    data: 'quantity'
                },
                {
                    data: 'uom'
                },
                {
                    data: 'manufacture_date'
                },
                {
                    data: 'created_by'
                },
                {
                    data: 'updated_by'
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

    $("#invtSearchForm").submit(function(event) {
        
        event.preventDefault();
        
        // Serialize the form data
        let searchFormData = $(this).serialize();

        invtDataTable_data.name = $(this).find('[name="name"]').val();
    
        invtDataTable.ajax.reload();
    });
    
</script>

