<form id="invtSearchForm">
    <div class="d-flex">
        <div class="d-flex align-items-center ms-auto mb-3">
            {{-- <div class="me-3">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" id="from_date" class="form-control" name="from_date" placeholder="From Date" />
            </div>
            <div class="me-3">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" id="to_date" class="form-control" name="to_date" placeholder="To Date" />
            </div> --}}
            <div class="me-3">
                <label for="FileName" class="form-label">File Name</label>
                <div class="input-group flex-nowrap">
                    <input type="text" class="form-control text-uppercase" name="FileName" placeholder="File Name" autofocus />
                    <button type="submit" class="btn btn-theme input-group-text"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
        
        
    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->
    <table id="invtDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                {{-- <th>FileName</th> --}}
                {{-- <th>WO</th>
                <th>PO_NO</th>
                <th>QTY</th> --}}
                {{-- <th>WD_To_JV_Price</th>
                <th>WD_To_JV_Total_Quotation</th> --}}
                {{-- <th>TransactionNo</th> --}}
                {{-- <th>Complete_QTY</th>
                <th>Complete_Date</th> --}}
                {{-- <th>Location</th>
                <th>JV_To_SMTT_Price</th>
                <th>JV_To_SMTT_Total_Quotation</th> --}}
                {{-- <th>organisation_id</th> --}}
               
                {{-- <th>Upload Date</th> --}}
                
                {{-- <th>created_by</th>
                <th>updated_by</th> --}}
                <th>File Namef</th>
                <th>Uploaded By</th>
                <th>Uploaded On</th>
                <th class="text-center">Action</th>
                
            </tr>
        </thead>
    </table>
</div>

<script>
    var invtDataTable_data =[];
    
    $(document).ready(function() {
        // function getThisWeekMonday() {
        //     var today = new Date();
        //     var dayOfWeek = today.getDay();
        //     var daysUntilMonday = (1 - dayOfWeek + 7) % 7; // Monday is day 1
        //     today.setDate(today.getDate() + daysUntilMonday);
        //     return today.toISOString().split('T')[0]; // Format the date as YYYY-MM-DD
        // }

        // // Function to find the next Sunday
        // function getThisWeekSunday() {
        //     var today = new Date();
        //     var dayOfWeek = today.getDay();
        //     var daysUntilSunday = (7 - dayOfWeek) % 7; // Sunday is day 0
        //     today.setDate(today.getDate() + daysUntilSunday);
        //     return today.toISOString().split('T')[0]; // Format the date as YYYY-MM-DD
        // }

        // // Set default date as this week's Monday and Sunday
        // var thisMonday = getThisWeekMonday();
        // var thisSunday = getThisWeekSunday();

        // // Set the 'from_date' and 'to_date' fields
        // $('#from_date').val(thisMonday);
        // $('#to_date').val(thisSunday);

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
                        'spacer', 'spacer',
                        {
                            text: '<i class="fa fa-plus-circle me-1"></i> Import',
                            className: 'btn btn-outline-theme',
                            action: function(e, dt, node, config) {
                                $('#modal').modal('show');
                                modal('{{ route('env.tmsv.sinvs.create', ['organisation' => $organisation->id]) }}',2);
                            }
                        }
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
                url: '{{ route('env.tmsv.sinvs.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.FileName = invtDataTable_data.FileName;
                    // d.from_date = $('#from_date').val(); 
                    // d.to_date = $('#to_date').val();  
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [
                { data:        'FileName'},
                { data:        'created_by',},
                // { data:        'WO',},
                // { data:        'PO_NO',},
                // { data:        'QTY',},
                // { data:        'WD_To_JV_Price',},
                // { data:        'WD_To_JV_Total_Quotation',},
                // // { data:        'TransactionNo',},
                // { data:        'Complete_QTY',},
                // { data:        'Complete_Date',},
                // // { data:        'Location',},
                // // { data:        'JV_To_SMTT_Price',},
        
                // // { data:        'organisation_id',},
                //                 { data:        'created_at',},
                // { data:        'created_by',},
                { data:        'latest_updated_at',},
                {
                    data: null,


                    mRender: function(data, type, row) {

                        var FileName = row['FileName'];
                        
                        return '<center><div class="dropdown">'    
                        +'<button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</button>'   
                        +'<ul class="dropdown-menu z-1">'
                        +'<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('env.tmsv.sinvs.import.form', ['organisation' => $organisation->id]) }}' + '\', \'' + FileName + '\')"><i class="bi bi-pencil-square"></i> Edit</a></li>' 
                        // +'<li><a href="#" class="dropdown-item" onClick="btnRemove(\'' +  username +'\')"><i class="bi bi-trash-fill"></i> Remove Role</a></li>'
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

    $("#invtSearchForm").submit(function(event) {
        
        event.preventDefault(); // Prevent default form submission

        // Capture the form data
        // invtDataTable_data.from_date = $(this).find('[name="from_date"]').val();
        // invtDataTable_data.to_date = $(this).find('[name="to_date"]').val();

        // // // Validate dates
        // if (invtDataTable_data.from_date && invtDataTable_data.to_date) {
        //     const fromDate = new Date(invtDataTable_data.from_date);
        //     const toDate = new Date(invtDataTable_data.to_date);

        //     if (fromDate > toDate) {
        //         alert("From Date cannot be later than To Date.");
        //         return;
        //     }
        // }

 
        // Serialize the form data
        let searchFormData = $(this).serialize();

        invtDataTable_data.FileName = $(this).find('[name="FileName"]').val();
    
        invtDataTable.ajax.reload();
    });
    
</script>

