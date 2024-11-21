@extends('layouts.app')

@section('title', 'View GRN')

@section('content')

    <!-- Display Success and Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Page Header and Breadcrumb -->
    <div class="d-flex align-items-center mb-4">
        <div class="p-2 flex-fill">
            <h1 class="page-header mb-0">View GRN</h1>
        </div>
        <div class="ms-auto p-2">
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home', ['organisation' => $organisation]) }}">SMTT</a></li>
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">GRN</a></li>
                <li class="breadcrumb-item active">View GRN</li>
            </ul>
        </div>
    </div>

    <!-- GRN Details Section -->
    <div class="row gx-4">
        <div class="col-lg-12"> 
            <!-- GRN Details Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="viewGrnForm" method="GET" enctype="multipart/form-data">
                        @csrf
                        <div class="row gx-4">
                            <!-- GRN Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center">
                                        <i class="bi bi-eye me-2"></i>
                                        <h5 class="card-title mb-0">GRN Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Receipt No -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Receipt No:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="receipt_no" value="{{ $grn->receipt }}" readonly>
                                            </div>
                                        </div>
                                        <!-- Warehouse -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Warehouse:</label>
                                            <div class="col-sm-8">
                                                <select class="form-select" name="warehouse" disabled>
                                                    <option value="{{$warehouse->id}}" selected disabled>{{$warehouse->name}}</option> 
                                                </select>
                                            </div>
                                        </div>
                                        <!-- Bill To -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Bill To:</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" rows="3" readonly>{{ $grn->bill }}</textarea>
                                            </div>
                                        </div>
                                        <!-- Ship To -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Ship To:</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control " name="ship_to" rows="4" readonly>
SUPERIOR LOGISTICS (HONG KONG)
KWU TUNG ROAD 105A DD98 LOT 6 
SHEUNG SHUI 999077 NEW 
TERRITORIES HONG KONG
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Optional Arrow Decorations -->
                                    <div class="card-arrow">
                                        <div class="card-arrow-top-left"></div>
                                        <div class="card-arrow-top-right"></div>
                                        <div class="card-arrow-bottom-left"></div>
                                        <div class="card-arrow-bottom-right"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Delivery Details -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center">
                                        <i class="bi bi-truck me-2"></i>
                                        <h5 class="card-title mb-0">Delivery Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Packing Slip -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Packing Slip:</label>
                                            <div class="col-sm-8">
                                                @if($grn->packing_slip_file)
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $grn->packing_slip }}" readonly>
                                                        <a href="{{ storage($grn->packing_slip_file) }}" class="btn btn-outline-secondary" download="{{ $grn->packing_slip}}">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{$grn->packing_slip}}" readonly>
                                                        <a href="#" class="btn btn-outline-secondary disabled">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Delivery Order (DO) -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Delivery Order (DO):</label>
                                            <div class="col-sm-8">
                                                @if($grn->do_file)
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $grn->do }}" readonly>
                                                        <a href="{{ asset($grn->do_file) }}" class="btn btn-outline-secondary" download="{{ $grn->do }}">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $grn->do }}" readonly>
                                                        <a href="#" class="btn btn-outline-secondary disabled">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Invoice -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Invoice:</label>
                                            <div class="col-sm-8">
                                                @if($grn->invoice_file)
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $grn->invoice }}" readonly>
                                                        <a href="{{ asset($grn->invoice_file) }}" class="btn btn-outline-secondary" download="{{ $grn->invoice }}">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $grn->invoice }}" readonly>
                                                        <a href="#" class="btn btn-outline-secondary disabled">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Receipt Date -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Receipt Date:</label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control" name="receipt_date" value="{{ \Carbon\Carbon::parse($grn->receipt_date)->format('Y-m-d') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-arrow">
                                        <div class="card-arrow-top-left"></div>
                                        <div class="card-arrow-top-right"></div>
                                        <div class="card-arrow-bottom-left"></div>
                                        <div class="card-arrow-bottom-right"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Back Button -->
                        <div class="d-flex justify-content-end mt-2">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

            <!-- Lines Section (if applicable) -->
            <div class="card">
                <div class="card-body">
                    <!-- Lines Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">Line Items</h2>
                    </div>
                    <!-- Lines Table -->
                    <table class="table table-bordered text-center" id="linesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Serial Number</th>
                                <th>Item Code</th>
                                <th>MPN</th>
                                <th>Location</th>
                                <th>Lot</th>
                                <th>Manufacture Date</th>
                                <th>Quantity</th>
                                <th>UOM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lines as $index => $line)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $line->serial_number }}</td>
                                    <td>{{ $line->item_code }}</td>
                                    <td>{{ $line->mpn }}</td>
                                    <td>{{ App\Models\Wms\Whmg\WmsWhmgLocts::find($line->location)->name }}</td>
                                    <td>{{ $line->lot }}</td>
                                    <td>{{ \Carbon\Carbon::parse($line->manufacture_date)->format('Y-m-d') }}</td>
                                    <td>{{ $line->quantity }}</td>
                                    <td>{{ $line->uom }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">No line items available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Optional Arrow Decorations -->
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>
        </div>
    </div>

@endsection