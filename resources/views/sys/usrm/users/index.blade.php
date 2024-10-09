<form id="userSearchForm">
    <div class="d-flex">
        <div class="ms-auto mb-3">
            <label class="form-label">Search</label>
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control text-uppercase" name="empID" placeholder="Emp ID" autofocus />
                <button type="submit" class="btn btn-theme input-group-text"><i class="bi bi-search"></i></button>
            </div>
        </div>

    </div>
</form>
<div class="hljs-container rounded-bottom">
    <!-- html -->


    <table id="userDataTable" class="table table-bordered table-xs w-100 fw-semibold text-nowrap mb-3">

        <thead>
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>

        </thead>
    </table>
</div>

<script>
    var userDatatable_data = [];

    $(document).ready(function() {
        var height = $(window).height() < 400 ? $(window).height() / 2.5 : true;


        $(window).trigger('resize');
        userDataTable = $('#userDataTable').DataTable({
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
                                modal('{{ route('sys.usrm.users.create', ['organisation' => $organisation->id]) }}',
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
                url: '{{ route('sys.usrm.users.show', ['organisation' => $organisation->id]) }}',
                data: function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.empID = userDatatable_data.empID;
                }
            },
            language: {
                "zeroRecords": "No matching records found",
            },
            columns: [{
                    data: 'username'
                },
                {
                    data: 'name'
                },
                {
                    data: 'phone_number'
                },
                {
                    data: 'email_address'
                },
                {
                    data: 'is_active'
                },

                {
                    data: null,


                    mRender: function(data, type, row) {
                        var username = row['username'];
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
                        +'<li><a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal" onclick="modal(\'' + '{{ route('sys.usrm.users.edit', ['organisation' => $organisation->id]) }}' + '\', \'' + username + '\')"><i class="bi bi-pencil-square"></i> Edit</a></li>'   
                        +'<li><a href="#" class="dropdown-item" onClick="btnResetPassword(\'' +  username +'\')"><i class="fa fa-refresh"></i> Reset Password</a></li>'
                        +'<li><a href="#" class="dropdown-item" onClick="' + actionFunction + '(\'' + username + '\')"><i class="' + actionIcon + '"></i> ' +  actionText + '</a></li>'
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
    function btnResetPassword(id) {
        $.ajax({
            method: "POST",
            url: '{{ route('sys.usrm.users.password-reset-default', ['organisation' => $organisation->id]) }}',
            dataType: 'json',
            data: {
                username: id,
                _token: '{{ csrf_token() }}' // Include the CSRF token
            },
            success: function(d) {
                if (d.status === 2) {
                    toast(2, d.message);
                } else {
                    toast(1, d.message);
                }


            },
            error: function(xhr, status, error) {
                alert('Error: ' + error.message);
            }
        });
    }

    function btnActivate(id) {
        $.ajax({
            method: "POST",
            url: '{{ route('sys.usrm.users.activate', ['organisation' => $organisation->id]) }}',
            dataType: 'json',
            data: {
                username: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(d) {
                if (d.status === 2) {
                    toast(2, d.message);
                    userDataTable.ajax.reload();
                } else {
                    toast(1, d.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error.message);
            }
        })
    }

    function btnDeactivate(id) {
        $.ajax({
            method: "POST",
            url: '{{ route('sys.usrm.users.deactivate', ['organisation' => $organisation->id]) }}',
            dataType: 'json',
            data: {
                username: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(d) {
                if (d.status === 2) {
                    toast(2, d.message);
                    userDataTable.ajax.reload();
                } else {
                    toast(1, d.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error.message);
            }
        })
    }

    $("#userSearchForm").submit(function(event) {

        event.preventDefault();

        // Serialize the form data
        let searchFormData = $(this).serialize();

        userDatatable_data.empID = $(this).find('[name="empID"]').val();

        userDataTable.ajax.reload();
    });
</script>
