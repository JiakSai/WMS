<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Tab Module Files Path</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
    <div class="mb-2 row" id="infoTable">
        <div class="col-md-12 mb-2">
            <table id="myTable" class="table table-bordered text-center order-list">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Name</td>
                        <td>Path</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Migration</td>
                        <td>{{ $migrationName }}</td>
                        <td>{{ $migrationPath }}</td>
                    </tr>
                    <tr>
                        <td>Model</td>
                        <td>{{ $modelName }}</td>
                        <td>{{ $modelPath }}</td>
                    </tr>
                    <tr>
                        <td>Controller</td>
                        <td>{{ $controllerName }}</td>
                        <td>{{ $controllerPath }}</td>
                    </tr>
                    <tr>
                        <td>View</td>
                        <td>index.blade.php</td>
                        <td>{{ $viewPath }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer d-flex">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
</div>