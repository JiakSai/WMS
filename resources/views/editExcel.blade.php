<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit {{$spreadsheet->name}}</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <div id="sheet-nav" class="mb-3">
            @foreach($sheets as $index => $sheet)
                <button type="button" class="btn btn-outline-primary sheet-button me-2" data-sheet-index="{{ $index }}">
                    {{ $sheet['name'] }}
                </button>
            @endforeach
        </div>

        <!-- Sheet Content -->
        <div id="editor">
            @foreach($sheets as $index => $sheet)
                <div class="sheet-content" data-sheet-index="{{ $index }}" style="{{ $index > 0 ? 'display:none;' : '' }}">
                    <!-- Raw HTML content -->
                    {!! $sheet['html'] !!}
                </div>
            @endforeach
        </div>
    </div>
    </div>
    <div class="modal-footer d-flex">
        <button type="button" class="btn btn-primary" id="save-button">Save
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    $(document).ready(function() {

        let editor = CKEDITOR.replace('editor', {
            allowedContent: true,
            height: '60vh',
            on: {
                instanceReady: function(evt) {
                    evt.editor.document.appendStyleSheet(
                        'table {border-collapse: collapse; width: 100%;} ' +
                        'td, th {border: 1px solid #ddd; padding: 8px;}'
                    );
                }
            }
        });

        // Initially hide all sheets except the first one
        $('.sheet-content').hide();
        // $('.sheet-content[data-sheet-index="0"]').show();

        // Handle sheet navigation button click
        $('.sheet-button').click(function() {
            let sheetIndex = $(this).data('sheet-index');

            // Hide all sheets and show the clicked one
            $('.sheet-content').hide();
            $('.sheet-content[data-sheet-index="' + sheetIndex + '"]').show();

            // Update active button state
            $('.sheet-button').removeClass('active');
            $(this).addClass('active');
        });

        // Initialize first sheet button as active
        $('.sheet-button').first().addClass('active');

        $('#save-button').click(function(e) {
            e.preventDefault();
            let htmlContent = editor.getData();

            $.ajax({
                url: '{{ route("editExcelSubmit", $spreadsheet->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    html: htmlContent
                },
                success: function(response) {
                    if (response.success) {
                        toast(2, 'Spreadsheet updated successfully');
                        $('#modal').modal('hide');
                        datatable.ajax.reload();
                    } else {
                        toast(1, 'Error updating spreadsheet');
                    }
                },
                error: function() {
                    toast(1, 'Error updating spreadsheet');
                }
            });
        });
    });
</script>