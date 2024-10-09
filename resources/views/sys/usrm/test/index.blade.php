{{-- <div class="d-flex align-items-center mb-3">
    <div class="ms-auto">
        <a href="#" class="btn btn-outline-theme" data-bs-toggle="modal" data-bs-target="#modal"
        onclick="modal('{{ route('addGroup', ['warehouse' => $warehouse->id]) }}', 2)"><i class="fa fa-plus-circle me-1"></i>Add Group</a>
    </div>
</div> --}}

<form id="groupSearchForm">
    <div class="row">
        <div class="col-lg-8">
            <div class="mb-3">
                <label class="form-label"></label>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="mb-3">
                <label class="form-label">Search</label>
                <div class="input-group flex-nowrap">
                    <input type="text" class="form-control" name="searchGroup"
                        placeholder="Group Name" autofocus/>
                    <button type="submit" class="btn btn-theme input-group-text"><i
                            class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->
    
    <table id="groupDataTable"  class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
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
    var groupDatatable_data =[];  

    function format(d) {
            return (
                '<table class="table table-bordered w-75 m-auto child-table text-center" id="child-table-' + d.id + '">' +
                '<thead>' +
                '<tr>' +
                '<th class="text-center">Username</th>' +
                '<th class="text-center">Name</th>' +
                '<th class="text-center">Action</th>' +
                '</tr>' +
                '</thead>' +
                '</table>'
            );
    }

    $(document).ready(function() {
        var height = $(window).height() < 400 ? $(window).height() /2.5 :true ;
        groupDatatable = $('#groupDataTable').DataTable({
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
                                modal('{{ route('addGroup', ['warehouse' => $warehouse->id]) }}', 2)
                            }
                        }
                    ]
                },
            },
            
            scrollY: true,//height,
            scrollX: true,
            fixedColumns: { left:0,right: 1 },
            destroy: true,
            processing: true,
            serverSide: true,
            searching: false,
            ordering: false,
            paging: true, // Enable pagination
            "ajax": {
                    method: "POST",
                    url: '{{ route('getGroupData', ['warehouse' => $warehouse->id]) }}',
                    data: function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.name = groupDatatable_data.searchGroup;
                    }
                },

            "language": {     "zeroRecords": "No matching records found",
            },
            'columns': [
                {
                    'className': 'dt-control',
                    'orderable': false,
                    'data': null,
                    'defaultContent': ''
                },
                { 'data': 'id' },
                { 'data': 'name' },
                { 'data': 'created_by' },
                { 'data' : 'updated_by'},
                {
                    mRender: function (data, type, row) {
                        $id = row['id'];
                      
                        return '<center><div class="dropdown">'    
                        +'<button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</button>'   
                        +'<ul class="dropdown-menu z-1">' 
                        +'<li><a href="#" class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('addGroupUser', ['warehouse' => $warehouse->id]) }}' + '\', \'' + $id + '\')"><i class="bi bi-person"></i> Add User</a></li>'        
                        +'<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('editGroup', ['warehouse' => $warehouse->id]) }}' + '\', \'' + $id + '\')"><i class="bi bi-pencil-square"></i> Edit</a></li>'   
                        +'<li><a href="#" class="dropdown-item" onClick="btnRemoveGroup(\''+$id +'\')"><i class="bi bi-trash"></i> Delete</a></li>'    
                        +'</ul>'    
                        +'</div></center>'  


                       
                    },
                },
            ],
           
        });

        function handleClick(button) {
        // Find the closest td element and set its z-index to 200
        const tdElement = button.closest("td");
        if (tdElement) {
            tdElement.style.zIndex = 200;
            console.log("z-index set to 200 for:", tdElement);
        }
    }

        $('#groupDataTable tbody').on('click', 'td.dt-control', function() {
            var tr = $(this).closest('tr');
            var row = groupDatatable.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                // $(this).html('<i class="bi bi-plus-circle"></i>');
            } else {
                // Open this row
                row.child(format(row.data())).show();
                tr.addClass('shown');
                // $(this).html('<i class="bi bi-dash-circle"></i>');

                // Initialize child DataTable
                $('#child-table-' + row.data().id).DataTable({
                    'processing': true,
                    'serverSide': true,
                    'searching': false,
                    'ordering': false,
                    'paging':false,
                    "bInfo" : false,
                    'ajax': {
                        'url': '{{route("getGroupChildData", ['warehouse' => $warehouse])}}',
                        'type': 'POST',
                        'data': {
                            '_token': '{{ csrf_token() }}',
                            'parentId': row.data().id
                        }
                    },
                    'columns': [
                        { 'data': 'username' },
                        { 'data': 'name' },
                        { 
                            mRender: function (data, type, row) {
                            $id = row['id'];

                            return '<center><button class="btn btn-dark btn-sm me-1 dropdown">'    
                            +'<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">'    
                            +'<i class="bi bi-list"></i> Menu'   
                            +'</a>'   
                            +'<div class="dropdown-menu">' 
                            +'<a href="#" class="dropdown-item" onClick="removeGroupUser(\''+$id +'\')"><i class="bi bi-trash"></i> Remove</a>'    
                            +'</div> '    
                            +'</button></center>'  
                            }, 
                        },
                    ],
                });
            }
        });

        document.querySelectorAll('a.toggle-vis').forEach((el) => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                let columnIdx = e.target.getAttribute('data-column');
                let column = groupDatatable.column(columnIdx);
                column.visible(!column.visible());
            });
        });

    });

    function btnRemoveGroup(id) {
        $.ajax({
            type: "POST",
            url: '{{route("deleteGroup", ['warehouse' => $warehouse])}}',
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
                groupDatatable.ajax.reload();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

    function removeGroupUser(Id){
        $.ajax({
            method: "POST",
            url : '{{route('removeGroupUser', ['warehouse'=> $warehouse])}}',
            dataType: 'json',
            data:{
                userId: Id,
                _token: '{{ csrf_token() }}'
            },
            success: function (d){
                if(d.status === 2){
                    toast(2, d.message);
                    groupDatatable.ajax.reload();
                }else{
                    toast(1, d.message);
                }
            },
            error: function(xhr, status, error){
                alert('Error: '+ error.message);
            }
        })
    }

    $("#groupSearchForm").submit(function(event) {
        
        event.preventDefault();
        
        // Serialize the form data
        let searchFormData = $(this).serialize();

        groupDatatable_data.searchGroup = $(this).find('[name="searchGroup"]').val();
    
        groupDatatable.ajax.reload();
    });
</script>
