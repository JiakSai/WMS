<form id="groupSearchForm">
    <div class="d-flex">
        <div class="ms-auto mb-3">
            <label class="form-label">Search</label>
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control text-uppercase" name="name" placeholder="Item Group Name" autofocus />
                <button type="submit" class="btn btn-theme input-group-text"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </div>
</form>

<div class="hljs-container rounded-bottom">
    <!-- html -->
    <table id="groupDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Name</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    var groupDataTable_data =[];

    function format(d) {
        return (
            '<table class="table table-bordered w-75 m-auto child-table text-center" id="child-table-' + d.id + '">' +
            '<thead>' +
            '<tr>' +
            '<th class="text-center">ID</th>' +
            '<th class="text-center">Name</th>' +
            '<th class="text-center">Action</th>' +
            '</tr>' +
            '</thead>' +
            '</table>'
        );
    }
    
    $(document).ready(function() {

        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;

        $(window).trigger('resize');
        groupDataTable = $('#groupDataTable').DataTable({
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
                                modal('{{ route('wms.item.grpcs.create', ['organisation' => $organisation->id]) }}',2);
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
                url: '{{ route('wms.item.grpcs.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.name = groupDataTable_data.name;
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [
                {
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: ''
                },
                {
                    data: 'id'
                },
                {
                    data: 'name'
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
                        var id = row['id'];

                        return '<center><div class="dropdown">'    
                        +'<button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</button>'   
                        +'<ul class="dropdown-menu dropdown-menu-end position-fixed">'
                        +'<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('wms.item.grpcs.create-child', ['organisation' => $organisation->id]) }}'+ '\', \'' + id + '\')"><i class="fa fa-plus-circle me-1"></i> Add Selection</a>'
                        +'<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('wms.item.grpcs.edit', ['organisation' => $organisation->id]) }}'+ '\', \'' + id + '\')"><i class="bi bi-pencil-square"></i> Edit</a>'  
                        +'<li><a href="#" class="dropdown-item" onClick="btnRemove(\'' +  id +'\')"><i class="bi bi-trash-fill"></i> Delete</a></li>'
                        +'</ul>'    
                        +'</div></center>'  
                    },
                },
            ],
        });

        $('#groupDataTable tbody').on('click', 'td.dt-control', function() {
            var tr = $(this).closest('tr');
            var row = groupDataTable.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');

                // Initialize child DataTable
                $('#child-table-' + row.data().id).DataTable({
                    processing: true,
                    serverSide: true,
                    searching: false,
                    ordering: false,
                    paging: false,
                    bInfo: false,
                    ajax: {
                        url: '{{ route('wms.item.grpcs.show-child-data', ['organisation' => $organisation->id]) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            parentId: row.data().id
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching child data:', error);
                        }
                    },
                    columns: [
                        { data: 'id' },
                        { data: 'name' },
                        {
                            mRender: function(data, type, row) {
                                var id = row['id'];
                                var parentId = row['parentId'];
                                return '<center><button class="btn btn-dark btn-sm me-1 dropdown">' +
                                    '<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">' +
                                    '<i class="bi bi-list"></i> Menu' +
                                    '</a>' +
                                    '<div class="dropdown-menu">' +
                                    '<a href="#" class="dropdown-item" onClick="btnRemoveChild(\'' + id + '\',\''+ parentId +'\')"><i class="bi bi-trash"></i> Remove</a>' +
                                    '</div>' +
                                    '</button></center>';
                            },
                        },
                    ],
                });
            }
        });

        $(window).trigger('resize');
    });

    function handleClick(button) {
        // Find the closest td element and set its z-index to 200
        const tdElement = button.closest("td");
        if (tdElement) {
            tdElement.style.zIndex = 200;
            console.log("z-index set to 200 for:", tdElement);
        }
    }

    function btnRemove(id) {
        $.ajax({
            type: "DELETE",
            url: '{{ route('wms.item.grpcs.destroy', ['organisation' => $organisation->id]) }}',
            dataType: 'json',
            data: { 
                id: id,
                _token: '{{ csrf_token() }}' // Include the CSRF token
            },
            success: function (d) {
                if (d.status === 2) {
                    toast(2, d.message);
                } else {
                    toast(1, d.message);
                }
                groupDataTable.ajax.reload();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

    function btnRemoveChild(id, parentId) {
        $.ajax({
            type: "POST",
            url: '{{ route('wms.item.grpcs.destroy-child', ['organisation' => $organisation->id]) }}',
            dataType: 'json',
            data: {
                childId: id,
                parentId: parentId,
                _token: '{{ csrf_token() }}' // Include the CSRF token
            },
            success: function(d) {
                if (d.status === 2) {
                    toast(2, d.message);
                } else {
                    toast(1, d.message);
                }
                groupDataTable.ajax.reload();
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

    $("#groupSearchForm").submit(function(event) {
        
        event.preventDefault();
        
        // Serialize the form data
        let searchFormData = $(this).serialize();

        groupDataTable_data.name = $(this).find('[name="name"]').val();
    
        groupDataTable.ajax.reload();
    });
    
</script>

