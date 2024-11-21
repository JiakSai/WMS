<!-- resources/views/wms/ship/ctrls/view.blade.php -->

@extends('layouts.app')

@section('title', 'View Shipment')

@section('content')

    <!-- Page Header and Breadcrumb -->
    <div class="d-flex align-items-center mb-4">
        <div class="p-2 flex-fill">
            <h1 class="page-header mb-0">View Shipment</h1>
        </div>
        <div class="ms-auto p-2">
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home', ['organisation' => $organisation]) }}">SMTT</a></li>
                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Shipment</a></li>
                <li class="breadcrumb-item active">View Shipment</li>
            </ul>
        </div>
    </div>

    <!-- Shipment Details Section -->
    <div class="row gx-4">
        <div class="col-lg-12"> 
            <!-- Shipment Details Card -->
            <div class="card">
                <div class="card-body">
                    <form id="viewShipmentForm" method="GET" enctype="multipart/form-data">
                        @csrf
                        <div class="row gx-4">
                            <!-- Shipment Information -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex align-items-center">
                                        <i class="bi bi-eye me-2"></i>
                                        <h5 class="card-title mb-0">Shipment Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Shipment No -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Shipment No:</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="shipment_no" value="{{ $shipment->shipment_no }}" readonly>
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
                                                <textarea class="form-control" rows="3" readonly>{{ $shipment->bill }}</textarea>
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
                                        <!-- Customs Slip -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Customs Slip:</label>
                                            <div class="col-sm-8">
                                                @if($shipment->customs_slip_file)
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $shipment->customs_slip }}" readonly>
                                                        <a href="{{ asset($shipment->customs_slip_file) }}" class="btn btn-outline-secondary" download="{{ $shipment->customs_slip}}">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $shipment->customs_slip }}" readonly>
                                                        <a href="#" class="btn btn-outline-secondary disabled">
                                                            <i class="bi bi-download"></i> Download
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Shipment Slip -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Shipment Slip:</label>
                                            <div class="col-sm-8">
                                                @if($shipment->shipment_slip_file)
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $shipment->shipment_slip }}" readonly>
                                                        <a href="{{ asset($shipment->shipment_slip_file) }}" class="btn btn-outline-secondary" download="{{ $shipment->shipment_slip}}">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $shipment->shipment_slip }}" readonly>
                                                        <a href="#" class="btn btn-outline-secondary disabled">
                                                            <i class="bi bi-download"></i> Download
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Invoice -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Invoice:</label>
                                            <div class="col-sm-8">
                                                @if($shipment->invoice_file)
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $shipment->invoice }}" readonly>
                                                        <a href="{{ asset($shipment->invoice_file) }}" download="{{ $shipment->invoice}}" class="btn btn-outline-secondary">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" value="{{ $shipment->invoice }}" readonly>
                                                        <a href="#"  class="btn btn-outline-secondary">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Shipment Date -->
                                        <div class="row mb-3">
                                            <label class="col-sm-4 col-form-label">Shipment Date:</label>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control" name="shipment_date" value="{{ $shipment->shipment_date }}" readonly>
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
                    </form>
                </div>
                <div class="card-arrow">
                    <div class="card-arrow-top-left"></div>
                    <div class="card-arrow-top-right"></div>
                    <div class="card-arrow-bottom-left"></div>
                    <div class="card-arrow-bottom-right"></div>
                </div>
            </div>

            <!-- Lines Section -->
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
            <!-- Back Button -->
            <div class="d-flex justify-content-end mt-2">
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>

@endsection