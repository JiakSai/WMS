<form id="roleSearchForm">
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
                    <input type="text" class="form-control" name="name"
                        placeholder="Role Name" autofocus/>
                    <button type="submit" class="btn btn-theme input-group-text"><i
                            class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->
    
    <table id="roleDataTable"  class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
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

    $(document).ready(function() {
        var height = $(window).height() < 400 ? $(window).height() /2.5 :true ;
        roleDataTable = $('#roleDataTable').DataTable({
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
                                modal('{{ route('sys.usrm.roles.create', ['organisation' => $organisation->id]) }}', 2)
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
                url: '{{ route('sys.usrm.roles.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.name = groupDatatable_data.searchGroup;
                }
            },

            "language": { "zeroRecords": "No matching records found" },
            'columns': [
                { 'data': 'id' },
                { 'data': 'name' },
                { 'data': 'created_by' },
                { 'data' : 'updated_by'},
                {
                    mRender: function (data, type, row) {
                        var id = row['id'];
                      
                        return '<center><div class="dropdown">'    
                        +'<button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</button>'   
                        +'<ul class="dropdown-menu z-1">' 
                        +'<a href="#" class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('sys.usrm.roles.edit', ['organisation' => $organisation->id]) }}' + '\', \'' + id + '\')"><i class="bi bi-pencil-square"></i> Edit</a>'    
                        +'<li><a href="#" class="dropdown-item" onClick="btnRemove(\'' + id + '\')"><i class="bi bi-trash"></i> Delete</a></li>'    
                        +'</ul>'    
                        +'</div></center>';
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

        document.querySelectorAll('a.toggle-vis').forEach((el) => {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                let columnIdx = e.target.getAttribute('data-column');
                let column = roleDataTable.column(columnIdx);
                column.visible(!column.visible());
            });
        });

    });

    function btnRemove(id) {
        $.ajax({
            type: "DELETE",
            url: '{{ route('sys.usrm.roles.destroy', ['organisation' => $organisation->id]) }}',
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
                roleDataTable.ajax.reload();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

    $("#roleSearchForm").submit(function(event) {
        event.preventDefault();
        
        // Serialize the form data
        let searchFormData = $(this).serialize();

        groupDatatable_data.searchGroup = $(this).find('[name="name"]').val();
    
        roleDataTable.ajax.reload();
    });
</script>