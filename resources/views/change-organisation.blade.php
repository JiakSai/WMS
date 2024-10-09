<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Select Organisation</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="addForm">
    <div class="modal-body">
        <div class="list-group" id="list-tab" role="tablist">
            @foreach($organisations as $organisation)
                <input class="list-group-item list-group-item-action col-sm-3 organisation-item @if($organisation->id == $active) active @endif" data-bs-toggle="list" name="{{$organisation->name}}" value="{{$organisation->name}}" role="tab" readonly>
            @endforeach
        </div>
        <input type="hidden" name="organisation" id="selectedOrganisation">
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary">Confirm
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Handle organisation item click
        $('.organisation-item').on('click', function() {
            // Remove active class from all items
            $('.organisation-item').removeClass('active');
            // Add active class to the clicked item
            $(this).addClass('active');
            // Set the selected organisation name in the hidden input
            $('#selectedOrganisation').val($(this).val());
        });

        // Handle form submission
        $("#addForm").submit(function(event) {
            // Prevent the default form submission
            event.preventDefault();
            let formDataArray = $(this).serializeArray();
            let formData = {};
            formDataArray.forEach(function(item) {
                formData[item.name] = item.value;
            });
            formData['_token'] = '{{ csrf_token() }}';

            $.ajax({
                method: "POST",
                url: '{{ route('change.organisation.status') }}',
                data: formData,
                success: function(d) {
                    if (d.status === 2) { 
                        window.location.href = d.route;
                        toast(2, d.message);
                    } else if (d.status === 1) {
                        let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                        toast(1, errorMessages);
                    }
                },
                error: function(xhr, status, error) {
                    toast(1, "Error:", xhr.status, status, error);
                }
            });
        });
    });
</script>