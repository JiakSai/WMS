<form id="mainsSearchForm">
    <div class="d-flex">
        <div class="ms-auto mb-3">
            <div class="input-group flex-nowrap">
                {{-- <div class="me-2">
                    <label for="name" class="form-label">Search</label>
                    <input type="text" id="name" class="form-control text-uppercase" name="InvoiceID" placeholder="Invoice" autofocus />
                </div> --}}
                <div class="me-2">
                    <label for="from_date_cndn" class="form-label">From Date</label>
                    <input type="datetime-local" id="from_date_cndn" class="form-control" name="from_date_cndn" placeholder="From Date" />
                </div>
                <div class="me-2">
                    <label for="to_date_cndn" class="form-label">To Date</label>
                    <input type="datetime-local" id="to_date_cndn" class="form-control" name="to_date_cndn" placeholder="To Date" />

                </div>
                <div class="me-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-theme form-control">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                {{-- <input type="hidden" id="from_date_cndn_hidden" name="from_date_cndn_hidden" />
                <input type="hidden" id="to_date_cndn_hidden" name="to_date_cndn_hidden" /> --}}
            </div>
        </div>
    </div>
</form>


<div class="hljs-container rounded-bottom">
    <!-- html -->
    <table id="CNDNDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                <th>DocumentType</th>
                <th>InvoiceID</th>
                <th>Type</th>
                <th>DocumentDate</th>
                {{-- <th>ddd</th> --}}
                <th>CusCode</th>
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
                <th>SoRef</th>
                <th>CusPo</th>
                <th>DoRef</th>
                <th>Terms</th>
                <th>InvoiceTotalAmount</th>
                <th>PartNo</th>
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
                {{-- <th>CustomForm1</th> --}}
                <th>AmtInMyr</th>
                
            </tr>
        </thead>
    </table>
</div>
<!-- Include Select2 CSS and JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    var CNDNDataTable_data =[];
    
    $(document).ready(function() {
        const today = new Date();
    let fromDate, toDate;

    // Check if today is Monday (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
    if (today.getDay() === 1) {
        // If today is Monday, set From Date to last Friday at 4 PM
        fromDate = new Date(today);
        fromDate.setDate(today.getDate() - 3); // Set date to last Friday
        fromDate.setHours(16, 0, 0); // Set time to 4 PM
    } else {
        // For any other day, set From Date to yesterday at 4 PM
        fromDate = new Date(today);
        fromDate.setDate(today.getDate() - 1);  
        fromDate.setHours(16, 0, 0); // Set time to 4 PM
    }

    // Set To Date to today at 4 PM
    toDate = new Date(today);
    toDate.setHours(16, 0, 0); // Set time to 4 PM

    oriToDate = toDate
    oriFromDate = fromDate
    // Convert dates to ISO string format
    fromDate = fromDate.toISOString().slice(0, 16); 
    toDate = toDate.toISOString().slice(0, 16);
    fromDate = fromDate.split('T')[0] + 'T16:00'; 
    toDate = toDate.split('T')[0] + 'T16:00'; 

    // Set the default values in the input fields
    $('#from_date_cndn').val(fromDate);
    $('#to_date_cndn').val(toDate);



        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;


        $(window).trigger('resize');
        CNDNDataTable = $('#CNDNDataTable').DataTable({
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
                        // {
                        //     extend: 'pdf',
                        //     text: '<i class="fa fa-file-pdf me-1"></i> PDF',
                        //     className: 'btn btn-outline-theme',

                        // }
                        // ,
                        'spacer', 'spacer',
                        {
                            text: '<i class="fa fa-plus-circle me-1"></i> Submit',
                            className: 'btn btn-outline-theme',
                            action: function(e, dt, node, config) {
                                $('#modal').modal('show');
                                modal('{{ route('env.sinv.cndns.create', ['organisation' => $organisation->id]) }}',2);
                            }
                        }


                    ]
                },
            },
            scrollY: true, 
            // scrollCollapse: true,
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
                url: '{{ route('env.sinv.cndns.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.name = CNDNDataTable_data.name;
                    d.from_date_cndn = $('#from_date_cndn').val(); 
                    d.to_date_cndn = $('#to_date_cndn').val();  
                }
            },

            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [
                { data: 'DocumentType' },
                { data: 'InvoiceID' },
                { data: 'Type' },
                { data: 'DocumentDate' },
                // { data: 'ddd' },
                { data: 'CusCode' },
                { data: 'TIN' },
                { data: 'BRN' },
                { data: 'CusRegName' },
                { data: 'CusAddress1' },
                { data: 'CusAddress2' },
                { data: 'CusAddress3' },
                { data: 'Country' },
                { data: 'City' },
                { data: 'StateCode' },
                { data: 'Tel' },
                { data: 'Currency' },
                { data: 'CurrencyRate' },
                { data: 'SoRef' },
                { data: 'CusPo' },
                { data: 'DoRef' },
                { data: 'Terms' },
                { data: 'InvoiceTotalAmount' },
                { data: 'PartNo' },
                { data: 'Classification' },
                { data: 'ItemDescription' },
                { data: 'OrderUOM' },
                { data: 'InvoiceQty' },
                { data: 'UnitPrice' },
                { data: 'TaxType' },
                { data: 'TaxAmount' },
                { data: 'TaxRate' },
                { data: 'TaxPrice' },
                { data: 'ItemAmt' },
                { data: 'ShipReceiptName' },
                { data: 'ShipAddress1' },
                { data: 'ShipAddress2' },
                { data: 'ShipAddress3' },
                { data: 'ShipCountry' },                
                { data: 'AmtInMyr' },              
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
    $("#mainsSearchForm").submit(function(event) {
    event.preventDefault(); // Prevent default form submission

    // Capture the form data
    CNDNDataTable_data.from_date_cndn = $(this).find('[name="from_date_cndn"]').val();
    CNDNDataTable_data.to_date_cndn = $(this).find('[name="to_date_cndn"]').val();

    // // Validate dates
    if (CNDNDataTable_data.from_date_cndn && CNDNDataTable_data.to_date_cndn) {
        const fromDate = new Date(CNDNDataTable_data.from_date_cndn);
        const toDate = new Date(CNDNDataTable_data.to_date_cndn);

        if (fromDate > toDate) {
            alert("From Date cannot be later than To Date.");
            return;
        }
    }

    // Get all InvoiceIDs from the DataTable
   
// Optionally reload the DataTable with the new filter parameters
CNDNDataTable.ajax.reload();

});



   
</script>
