{{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

<div class="modal-header">
    <h1 class="modal-title fs-5" id="exampleModalLabel">Submit Invoice</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="addForm">
    <div class="modal-body" style="max-height: calc(100vh - 200px); overflow-y: auto;">
        <div class="mb-3 row" id="formContent">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Exclude Invoice</label>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="name[]" placeholder="Enter invoice to exclude">
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-danger" onclick="removeInvoiceField(this)">Remove</button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-3" onclick="addInvoiceField()">Add More</button>
    </div>
    <div class="modal-footer d-flex">
        <button type="submit" class="btn btn-primary" id="submitButton">Submit
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
    </div>
</form>
<!-- Include Select2 CSS and JS -->
{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script> --}}


<script>
               function addInvoiceField() {
        const formContent = document.getElementById('formContent');

        const newField = document.createElement('div');
        newField.classList.add('row', 'mb-3');
        newField.innerHTML = `
            <label class="col-sm-3 col-form-label">Exclude Invoice</label>
            <div class="col-sm-7">
                <input type="text" class="form-control" name="name[]" placeholder="Enter invoice to exclude">
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-danger" onclick="removeInvoiceField(this)">Remove</button>
            </div>
        `;

        formContent.appendChild(newField);
    }

    function removeInvoiceField(button) {
        const fieldRow = button.parentNode.parentNode;
        fieldRow.remove();
    }
    $("#addForm").submit(function(event) {
    event.preventDefault();

    let table = $('#InvoiceDataTable').DataTable();
    let tableData = table.rows().data().toArray();

    // Get the values from the input text boxes for excluded invoices
    let excludedInvoices = [];
    $('#formContent input[name="name[]"]').each(function() {
        excludedInvoices.push($(this).val().trim());
    });

    // Filter out the invoices with matching invoiceID (assuming 'InvoiceID' is in your table data)
    let filteredData = tableData.filter(row => {
        return !excludedInvoices.includes(row['InvoiceID']);  // Adjust 'InvoiceID' as per your table column name
    });

    // If no data left after filtering
    if (filteredData.length === 0) {
        alert('No data available to export after exclusion.');
        return;
    }

    // Calculate the sum of AmtInMyr from the filtered data
    let totalAmtInMyr = filteredData.reduce((sum, row) => {
        return sum + (parseFloat(row['AmtInMyr']) || 0); // Replace 'AmtInMyr' with the actual column name
    }, 0);

    // Show confirmation message with the sum of AmtInMyr
    let confirmationMessage = `Are you sure you want to submit the invoice?\n\nTotal Amount (AmtInMyr): ${totalAmtInMyr.toFixed(2)}`;
    let isConfirmed = confirm(confirmationMessage);

    if (!isConfirmed) {
        return;  // If user cancels, don't proceed with the submission
    }

    // Proceed with form submission
    let submitButton = $(this).find('button[type="submit"]');
    let spinner = submitButton.find('.spinner-border');
    spinner.removeClass('d-none');
    submitButton.prop('disabled', true);

    // Fetch the template CSV headers
    fetchTemplateCSV()
    .then(templateHeaders => {
        let csvData = generateCSVFromTableData(filteredData, templateHeaders);
        downloadCSV(csvData);  // Trigger CSV download
    })
    .catch(error => {
        console.error("Error fetching template CSV:", error);
        spinner.addClass('d-none');
        submitButton.prop('disabled', false);
    });
});


// Function to fetch template headers from a CSV file
function fetchTemplateCSV() {
    return new Promise((resolve, reject) => {
        fetch('/Template/SaleInvoice_Template.csv')
            .then(response => response.text())
            .then(data => {
                // Ensure the data fetched is a plain text CSV and split into rows
                let lines = data.trim().split('\n');
                let headers = lines[0].split('|').map(header => header.trim()); // Get the first row as headers
                resolve(headers);
            })
            .catch(error => reject(error));
    });
}

// Define the template headers and column mapping
const columnMapping = {
    "DocType" :"DocumentType",
"RefNo" :"InvoiceID",
"DocDate" :"DocumentDate",
// "CustVendCode" :"CusCode",
"TIN" :"TIN",
"BRN" :"BRN",
"BNPType" :"BNPType",
"SSTNo" :"SSTNo",
"MSICCode" :"MSICCode",
"CustVendName" :"CusRegName",
"Address1" :"CusAddress1",
"Address2" :"CusAddress2",
"Address3" :"CusAddress3",
"City" :"City",
"State" :"StateCode",
"Country" :"Country",
"TelNo" :"Tel",
"Currency" :"Currency",
"CurrencyRate" :"CurrencyRate",
"HeaderRemark" :"SoRef",
"HeaderReference" :"CusPo",
"HeaderDescription" :"DoRef",
"Terms" :"Terms",
"TotalAmount" :"InvoiceTotalAmount",
"PartNo" :"PartNo",
"Classification" :"Classification",
"Description" :"ItemDescription",
"OrderQty" :"InvoiceQty",
"OrderUOM" :"OrderUOM",
"UnitPrice" :"UnitPrice",
"TaxType" :"TaxType",
"TaxRate" :"TaxRate",
"TaxAmount" :"TaxAmount",
"TaxInPriceAmount" :"TaxPrice",
"Amount" :"ItemAmt",
"ShipReceiptName" :"ShipReceiptName",
"ShipReceiptAddress1" :"ShipAddress1",
"ShipReceiptAddress2" :"ShipAddress2",
"ShipReceiptAddress3" :"ShipAddress3",
"ShipReceiptCountry" :"ShipCountry"
};

function generateCSVFromTableData(tableData, templateHeaders) {
    let csvRows = [];

    // Add the template headers to CSV
    csvRows.push(templateHeaders.join('|'));

    // Process each row from the DataTable
    tableData.forEach(function(row) {
        let rowData = [];

        // Loop through the template headers and extract the corresponding data
        templateHeaders.forEach(function(header) {
            if (columnMapping[header] !== undefined) {
                // If there's a mapping, push the value from the DataTable's row
                rowData.push(row[columnMapping[header]]);
            } else {
                rowData.push('');  // If no mapping, add an empty value
            }
        });

        // Add the row data to the CSV (joined by pipe delimiter)
        csvRows.push(rowData.join('|'));
    });

    // Return the CSV content
    return csvRows.join('\n');
}


// Function to trigger CSV file download
// function downloadCSV(csvData) {
//     let csvFile = new Blob([csvData], { type: 'text/csv' }); // Ensure it's a text/csv type
//     let downloadLink = document.createElement('a');
//     downloadLink.href = URL.createObjectURL(csvFile);
//     downloadLink.download = 'invoice_data.csv'; // You can change the file name here
//     downloadLink.click();
// }

function downloadCSV(csvData) {
    // Send the CSV data to the server to be saved in a folder
    $.ajax({
        method: "POST",
        // url: "env.sinv.sinvs.save-csv"
        url: '{{ route('env.sinv.sinvs.save-ftp', ['organisation' => $organisation->id]) }}',
        data: { 
            _token: '{{ csrf_token() }}',
            csvData: csvData 
        },
        success: function(response) {
            let submitButton = $("#addForm").find('button[type="submit"]');
            let spinner = submitButton.find('.spinner-border');
            spinner.addClass('d-none');
            submitButton.prop('disabled', false);
            alert(response.message); // Display success message
        },
        error: function(xhr, status, error) {
            let submitButton = $("#addForm").find('button[type="submit"]');
            let spinner = submitButton.find('.spinner-border');
            spinner.addClass('d-none');
            submitButton.prop('disabled', false);
            console.error("Error saving CSV:", error);
        }
    });
}



    //     $.ajax({
    //         method: "POST",
    //         url: '{{ route('wms.whmg.whmgs.store', ['organisation' => $organisation->id]) }}',
    //         data: formData,
    //         success: function(d) {
    //             // Hide the spinner
    //             spinner.addClass('d-none');
    //             submitButton.prop('disabled', false);

    //             if (d.status === 2) { 

    //                 $("#modal").modal('hide');

    //                 groupDataTable.ajax.reload();
                    
    //                 toast(2, d.message);

    //             } else if (d.status === 1) {
    //                 let errorMessages = d.errors.map(error => `Error: ${error}`).join('<br>');
    //                 toast(1, errorMessages);
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             // Hide the spinner
    //             spinner.addClass('d-none');
    //             submitButton.prop('disabled', false);

    //             toast(1,"Error:", xhr.status, status, error);
    //         }
    //     });
    // });
</script>