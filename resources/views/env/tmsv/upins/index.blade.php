<form id="UploadinvtSearchForm">
    <div class="d-flex">
        <div class="d-flex align-items-center ms-auto mb-3">
            <div class="me-3">
                <label for="from_date_up" class="form-label">From Date</label>
                <input type="date" id="from_date_up" class="form-control" name="from_date_up" placeholder="From Date" />
            </div>
            <div class="me-3">
                <label for="to_date_up" class="form-label">To Date</label>
                <input type="date" id="to_date_up" class="form-control" name="to_date_up" placeholder="To Date" />
            </div>
            <div class="me-3">
                <label for="PN" class="form-label">Item Name</label>
                <div class="input-group flex-nowrap">
                    <input type="text" class="form-control text-uppercase" name="PN" placeholder="Item Name" autofocus />
                    <button type="submit" class="btn btn-theme input-group-text"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
        
        
    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->
    <table id="UploadinvtDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                <th>DocumentType</th>
                <th>InvoiceID</th>
                <th>DocumentDate</th>        
                <th>TIN</th>
                <th>BRN</th>
                <th>CusRegName</th>
                <th>CusAddress1</th>
                <th>CusAddress2</th>
                <th>CusAddress3</th>
                <th>Country</th>
                <th>City</th>
                <th>StateCode</th>
                <th>Tel</th>
                <th>Currency</th>
                <th>CurrencyRate</th>
                <th>Terms</th>
                <th>Classification</th>
                <th>ItemDescription</th>
                <th>OrderUOM</th>
                <th>InvoiceQty</th>
                <th>UnitPrice</th>
                <th>TaxType</th>
                <th>TaxRate</th>
                <th>TaxAmount</th>
                <th>TaxPrice</th>
                <th>ItemAmt</th>
                <th>ShipReceiptName</th>
                <th>ShipAddress1</th>
                <th>ShipAddress2</th>
                <th>ShipAddress3</th>
                <th>ShipCountry</th>               
            </tr>
        </thead>
    </table>
</div>

<script>
    var UploadinvtDataTable_data =[];
    
    $(document).ready(function() {
        function getThisWeekMonday() {
            var today = new Date();
            var dayOfWeek = today.getDay();
            var daysUntilMonday = (1 - dayOfWeek + 7) % 7; // Monday is day 1
            today.setDate(today.getDate() + daysUntilMonday);
            return today.toISOString().split('T')[0]; // Format the date as YYYY-MM-DD
        }

        // Function to find the next Sunday
        function getThisWeekSunday() {
            var today = new Date();
            var dayOfWeek = today.getDay();
            var daysUntilSunday = (7 - dayOfWeek) % 7; // Sunday is day 0
            today.setDate(today.getDate() + daysUntilSunday);
            return today.toISOString().split('T')[0]; // Format the date as YYYY-MM-DD
        }

        // Set default date as this week's Monday and Sunday
        var thisMonday = getThisWeekMonday();
        var thisSunday = getThisWeekSunday();

        // Set the 'from_date_up' and 'to_date_up' fields
        $('#from_date_up').val(thisMonday);
        $('#to_date_up').val(thisSunday);

        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;

        $(window).trigger('resize');
        UploadinvtDataTable = $('#UploadinvtDataTable').DataTable({
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
                url: '{{ route('env.tmsv.upins.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.PN = UploadinvtDataTable_data.PN;
                    d.from_date_up = $('#from_date_up').val(); 
                    d.to_date_up = $('#to_date_up').val();  
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [
                {data:'DocumentType'},
                {data:'InvoiceID'},
                {data:'DocumentDate'},
                {data:'TIN'},
                {data:'BRN'},
                {data:'CusRegName'},
                {data:'CusAddress1'},
                {data:'CusAddress2'},
                {data:'CusAddress3'},
                {data:'Country'},
                {data:'City'},
                {data:'StateCode'},
                {data:'Tel'},
                {data:'Currency'},
                {data:'CurrencyRate'},
                {data:'Terms'},
                {data:'Classification'},
                { data:        'PN'},
                {data:'OrderUOM'},
                { data:        'Complete_QTY',},
                { data:        'WD_To_JV_Price',},
                {data:'TaxType'},
                {data:'TaxRate'},
                {data:'TaxAmount'},
                {data:'TaxPrice'},
                { data:        'WD_To_JV_Total_Quotation',},
                {data:'ShipReceiptName'},
                {data:'ShipAddress1'},
                {data:'ShipAddress2'},
                {data:'ShipAddress3'},
                {data:'ShipCountry'},

        
   
     


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

    $("#UploadinvtSearchForm").submit(function(event) {
        
        event.preventDefault(); // Prevent default form submission

        // Capture the form data
        UploadinvtDataTable_data.from_date_up = $(this).find('[name="from_date_up"]').val();
        UploadinvtDataTable_data.to_date_up = $(this).find('[name="to_date_up"]').val();

        // // Validate dates
        if (UploadinvtDataTable_data.from_date_up && UploadinvtDataTable_data.to_date_up) {
            const fromDate = new Date(UploadinvtDataTable_data.from_date_up);
            const toDate = new Date(UploadinvtDataTable_data.to_date_up);

            if (fromDate > toDate) {
                alert("From Date cannot be later than To Date.");
                return;
            }
        }

 
        // Serialize the form data
        let searchFormData = $(this).serialize();

        UploadinvtDataTable_data.PN = $(this).find('[name="PN"]').val();
    
        UploadinvtDataTable.ajax.reload();
    });
    
</script>

