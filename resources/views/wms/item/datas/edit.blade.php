<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Item Data</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
<div class="modal-body" style="max-height: calc(100vh - 200px);overflow-y: auto;">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="itemTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="item-tab" data-bs-toggle="tab" data-bs-target="#item" type="button" role="tab">Item Data</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="group-tab" data-bs-toggle="tab" data-bs-target="#group" type="button" role="tab">Item Grouping</button>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content mt-3" id="itemTabsContent">
            <!-- Item Data Tab -->
            <div class="tab-pane fade show active" id="item" role="tabpanel">
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Item Name</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="name" value="{{$itemData->name}}">
                        <input type="hidden" class="form-control" name="id" value="{{$itemData->id}}">
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-3 col-form-label">Description</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="description" value="{{$itemData->description}}">
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-12">
                        <fieldset class="border p-3">
                            <legend class="float-none w-auto px-2 fs-6">Characteristics</legend>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Item Type</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="type">
                                        <option value="" selected disabled>Select</option>
                                        <option value="Purchased" @if ($itemData->type == "Purchased") selected @endif>Purchased</option>
                                        <option value="Manufactured" @if ($itemData->type == "Manufactured") selected @endif >Manufactured</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-12">
                        <fieldset class="border p-3">
                            <legend class="float-none w-auto px-2 fs-6">Unit Data</legend>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Unit Set</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="unit_set">
                                        <option value="" selected disabled>Select</option>
                                        <option value="STD"  @if ($itemData->unit_set == "STD") selected @endif>STD</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Inventory Unit</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="inventory_unit">
                                        <option value="" selected disabled>Select</option>
                                        <option value="pcs" @if ($itemData->inventory_unit == "pcs") selected @endif>pcs</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Weight Unit</label>
                                <div class="col-sm-9">
                                    <select class="form-select" name="weight_unit">
                                        <option value="" selected disabled>Select</option>
                                        <option value="kgs" @if ($itemData->weight_unit == "kgs") selected @endif>kgs</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label">Weight</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="weight" value="{{$itemData->weight}}">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>

            <!-- Item Grouping Tab -->
            <div class="tab-pane fade" id="group" role="tabpanel">
                @foreach ($groupParents as $groupParent)
                    <div class="mb-3 row">
                        <label class="col-sm-3 col-form-label">{{$groupParent->name}}</label>
                        <div class="col-sm-9">
                            <select class="form-select" name="{{$groupParent->id}}">
                                <option value="">Select</option>
                                @foreach ($groupChilds as $groupChild)
                                    @if ($groupChild->parent_id == $groupParent->id)
                                        <option value="{{$groupChild->id}}" 
                                            {{ in_array($groupChild->id, $selectedGroupChilds ?? [], true) ? 'selected' : '' }}>
                                            {{$groupChild->name}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary" id="submitButton">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    $("#addForm").submit(function(event) {
        // Prevent the default form submission
        event.preventDefault();

        // Show the spinner
        let submitButton = $(this).find('button[type="submit"]');
        let spinner = submitButton.find('.spinner-border');
        spinner.removeClass('d-none');
        submitButton.prop('disabled', true);

        let formDataArray = $(this).serializeArray();
        let formData = {};
        formDataArray.forEach(function(item) {
            if (formData[item.name]) {
                if (Array.isArray(formData[item.name])) {
                    formData[item.name].push(item.value);
                } else {
                    formData[item.name] = [formData[item.name], item.value];
                }
            } else {
                formData[item.name] = item.value;
            }
        });

        formData['_token'] = '{{ csrf_token() }}';

        $.ajax({
            method: "POST",
            url: '{{ route('wms.item.datas.update', ['organisation' => $organisation->id]) }}',
            data: formData,
            success: function(d) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                if (d.status === 2) { 

                    $("#modal").modal('hide');

                    itemDataTable.ajax.reload();
                    
                    toast(2, d.message);

                } else if (d.status === 1) {
                    let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
                    toast(1, errorMessages);
                }
            },
            error: function(xhr, status, error) {
                // Hide the spinner
                spinner.addClass('d-none');
                submitButton.prop('disabled', false);

                toast(1,"Error:", xhr.status, status, error);
            }
        });
    });
</script>