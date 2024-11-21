<form id="tabSearchForm">
    <div class="d-flex">
        <div class="ms-auto mb-3">
            <label class="form-label">Search</label>
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control text-uppercase" name="name" placeholder="Tab Name" autofocus />
                <button type="submit" class="btn btn-theme input-group-text"><i class="bi bi-search"></i></button>
            </div>
        </div>
    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->
    <table id="tabDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Code</th>
                <th>Main Group</th>
                <th>Sub Group</th>
                <th>Created By</th>
                <th>Updated By</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    var tabDataTable_data =[];
    
    $(document).ready(function() {
        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;


        $(window).trigger('resize');
        tabDataTable = $('#tabDataTable').DataTable({
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
                                modal('{{ route('sys.modu.tabms.create', ['organisation' => $organisation->id]) }}',
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
                url: '{{ route('sys.modu.tabms.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.name = tabDataTable_data.name;
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'name'
                },
                {
                    data: 'code'
                },
                {
                    data: 'main_group'
                },
                {
                    data: 'sub_group'
                },
                {
                    data: 'created_by'
                },
                {
                    data: 'updated_by'
                },
                {
                    data: 'is_active'
                },
                {
                    data: null,


                    mRender: function(data, type, row) {
                        var id = row['id'];
                        var isActive = row['is_active'] === 'Active';
                        var actionText = isActive ? 'Deactivate' : 'Activate';
                        var actionIcon = isActive ? 'bi bi-person-fill-x' :
                            'bi bi-person-check-fill';
                        var actionFunction = isActive ? 'btnDeactivate' : 'btnActivate';

                        return '<center><div class="dropdown">'    
                        +'<button class="btn dt-buttons btn-dark btn-sm me-1 pt-0 pb-0 dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="handleClick(this)">'    
                        +'<i class="bi bi-list"></i> Menu'   
                        +'</button>'   
                        +'<ul class="dropdown-menu z-1">'
                        +'<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('sys.modu.tabms.show-path', ['organisation' => $organisation->id]) }}'+ '\', \'' + id + '\')"><i class="bi bi-folder2-open"></i> Show Path</a></li>' 
                        +'<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('sys.modu.tabms.edit', ['organisation' => $organisation->id]) }}' + '\', \'' + id + '\')"><i class="bi bi-pencil-square"></i> Edit</a></li>'                          
                        +'<li><a href="#" class="dropdown-item" onClick="btnRemove(\'' +  id +'\')"><i class="bi bi-trash-fill"></i> Delete</a></li>'
                        +'<li><a href="#" class="dropdown-item" onClick="' + actionFunction + '(\'' + id + '\')"><i class="' + actionIcon + '"></i> ' +  actionText + '</a></li>'
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
            tdElement.style.zIndex = 200;
            console.log("z-index set to 200 for:", tdElement);
        }
    }

    function btnRemove(id) {
        $.ajax({
            type: "DELETE",
            url: '{{route('sys.modu.tabms.destroy', ['organisation' => $organisation->id])}}',
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
                tabDataTable.ajax.reload();
            },
            error: function (xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

    function btnActivate(id){
        $.ajax({
            method: "POST",
            url : '{{route('sys.modu.tabms.activate', ['organisation' => $organisation->id])}}',
            dataType: 'json',
            data:{
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function (d){
                if(d.status === 2){
                    toast(2, d.message);
                    tabDataTable.ajax.reload();
                }else{
                    toast(1, d.message);
                }
            },
            error: function(xhr, status, error){
                alert('Error: '+ error.message);
            }
        })
    }

    function btnDeactivate(id){
        $.ajax({
            method: "POST",
            url : '{{route('sys.modu.tabms.deactivate', ['organisation' => $organisation->id])}}',
            dataType: 'json',
            data:{
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function (d){
                if(d.status === 2){
                    toast(2, d.message);
                    tabDataTable.ajax.reload();
                }else{
                    toast(1, d.message);
                }
            },
            error: function(xhr, status, error){
                alert('Error: '+ error.message);
            }
        })
    }

    $("#tabSearchForm").submit(function(event) {
        
        event.preventDefault();
        
        // Serialize the form data
        let searchFormData = $(this).serialize();

        tabDataTable_data.name = $(this).find('[name="name"]').val();
    
        tabDataTable.ajax.reload();
    });
    
</script>
