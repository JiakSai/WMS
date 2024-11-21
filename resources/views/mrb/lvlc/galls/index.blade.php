<form id="mainSearchForm">
    <div class="d-flex">
        <div class="ms-auto mb-3">
            <label class="form-label">Search</label>
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control text-uppercase" name="name" placeholder="Group Name" autofocus />
                <button type="submit" class="btn btn-theme input-group-text"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->
    <table id="mainDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                <th rowspan="2">Group Name</th>
                <th rowspan="2">Total User</th>
                <th colspan="7" class="text-center">Level</th>
                <th rowspan="2">Created / Updated By</th>
                <th rowspan="2" class="text-center">Action</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    var mainDataTable_data =[];
    
    $(document).ready(function() {
        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;


        $(window).trigger('resize');
        mainDataTable = $('#mainDataTable').DataTable({
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
                                $('#modal').modal('show');
                                modal('{{ route('mrb.lvlc.galls.create', ['organisation' => $organisation->id]) }}',
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
                url: '{{ route('mrb.lvlc.galls.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.name = mainDataTable_data.name;
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [

                {
                    data: 'group_name'
                },
                {
                    data: 'total_user'
                },
                {
                    data: 'level_1'
                },
                {
                    data: 'level_2'
                },
                {
                    data: 'level_3'
                },                
                {
                    data: 'level_4'
                },
                {
                    data: 'level_5'
                },   
                {
                    data: 'level_6'
                }, 
                {
                    data: 'level_7'
                },  
                {
                    data: 'created_by'
                },
                {
                    data: null,


                    mRender: function(data, type, row) {

                        var id = row['group_id'];
                        return '<center><div class="dropdown">'    
                        +'<button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</button>'   
                        +'<ul class="dropdown-menu z-1">'
                        +'<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('mrb.lvlc.galls.edit', ['organisation' => $organisation->id]) }}' + '\', \'' + id + '\')"><i class="bi bi-eye"></i> View Available User</a></li>' 
                        +'<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('mrb.lvlc.galls.edit', ['organisation' => $organisation->id]) }}' + '\', \'' + id + '\')"><i class="bi bi-pencil-square"></i> Edit</a></li>' 
                        +'</ul>'    
                        +'</div></center>'  
                    },
                },
            ],
            autoWidth: false,
            columnDefs: [
                { className: "text-center", targets: [1,2,3,4,5,6,7,8,10] }
            ]
        });
        $(window).trigger('resize');
    });


    function btnRemove(id) {
        $.ajax({
            type: "DELETE",
            url: '{{route('qms.role.asgns.destroy', ['organisation' => $organisation->id])}}',
            dataType: 'json',
            data: { 
                username: id,
                _token: '{{ csrf_token() }}' // Include the CSRF token
            },
            success: function (d) {
                if (d.status === 2) {
                    toast(2, d.message);
                } else {
                    toast(1, d.message);
                }
                mainDataTable.ajax.reload();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

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

    $("#mainSearchForm").submit(function(event) {
        
        event.preventDefault();
        
        // Serialize the form data
        let searchFormData = $(this).serialize();

        mainDataTable_data.name = $(this).find('[name="name"]').val();
    
        mainDataTable.ajax.reload();
    });
    
</script>
