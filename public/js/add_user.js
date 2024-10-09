
$("#addForm").submit(function(event) {
    // Prevent the default form submission
    event.preventDefault();
    let  formData = $(this).serialize();

    $.ajax({
        method: "POST",
        url: '/wms/userControl/addUserSubmit',
        data: {
            formData,
            _token: crsfToken,
        },
        success: function(d) {
            
            if (d.status === 2) { 
                $("#modal").modal('hide');
                datatable.ajax.reload();
            }
            toast(d.status,d.message);
           
            
        },
        error: function(xhr, status, error) {
            toast(1,"Error:", xhr.status, status, error);
        }
    });
});