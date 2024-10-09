@extends('layouts.app')

@section('title', 'Warehouse Management System')

@section('content')
<div class="row" id="todayWO">
    <div class="col-lg-12">
        <div id="statsWidget" class="mb-5">
            <h4>Warehouse Management System</h4>
            <p>Managing your warehouses, handling orders, and transferring stock have now become easier. With Zoho Inventory, controlling items across locations is free, easy-to-use, and quick. SKU Generator. Barcode Scanning.</p>

            @foreach($mainModules as $mainModule)
                <div class="card mb-5">
                    <div class="card-header">
                        {{  $mainModule->name }}
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">

                            @foreach ($subModules as $subModule)
                                @if($subModule->group == $mainModule->id)
                                    <div class="col-xl-3 col-md-4 col-sm-6 col-xs-12 mb-3">
                                        <a href="{{ route('select-module', ['organisation' => $organisation->id, 'subModule'=> $subModule->id]) }}" class="card text-decoration-none h-100">
                                            <div
                                                class="card-body d-flex align-items-center text-inverse m-5px bg-inverse bg-opacity-10">
                                                <div class="flex-fill">
                                                    <div class="mb-1">Today, 11:25AM</div>
                                                    <h2>{{ $subModule->name }}</h2>
                                                    <div>{{ $subModule->description }}</div>
                                                </div>
                                                <div class="opacity-5">
                                                    <i class="{{ $subModule->icon }} fa-4x"></i>
                                                </div>
                                            </div>
                                            <div class="card-arrow">
                                                <div class="card-arrow-top-left"></div>
                                                <div class="card-arrow-top-right"></div>
                                                <div class="card-arrow-bottom-left"></div>
                                                <div class="card-arrow-bottom-right"></div>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="card-arrow">
                        <div class="card-arrow-top-left"></div>
                        <div class="card-arrow-top-right"></div>
                        <div class="card-arrow-bottom-left"></div>
                        <div class="card-arrow-bottom-right"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection