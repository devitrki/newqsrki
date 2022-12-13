@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Daily Inventory Warehouse') }}</p>
    </div>
    <div class="col-12">
        <div class="row head-item-row">
            <div class="col-12 col-md-4 head-item">
                <strong>Outlet :</strong> {{ __($header['plant']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>Date :</strong> {{ __($header['date']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>MOD :</strong>
                @isset($header['appReview']->mod_pic)
                    {{ $header['appReview']->mod_pic }}
                @else
                    -
                @endisset
            </div>
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Product Name</th>
                    <th scope="col">UOM</th>
                    <th scope="col">Frekuensi</th>
                    <th scope="col">Stock Opening </th>
                    <th scope="col">GR Plant (IN)</th>
                    <th scope="col">DC (IN)</th>
                    <th scope="col">Vendor (IN)</th>
                    <th scope="col">Section (IN)</th>
                    <th scope="col">GI Plant (OUT)</th>
                    <th scope="col">DC (OUT)</th>
                    <th scope="col">Vendor (OUT)</th>
                    <th scope="col">Section (OUT)</th>
                    <th scope="col">Stock Closing</th>
                    <th scope="col">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="Product Name">{{ $item->product_name }}</td>
                        <td data-label="UOM">{{ $item->uom }}</td>
                        <td data-label="Frekuensi">{{ $item->frekuensi }}</td>
                        <td data-label="Opening Stock">{{ $item->stock_opening }}</td>
                        <td data-label="GR Plant (IN)">{{ $item->stock_in_gr_plant }}</td>
                        <td data-label="DC (IN)">{{ $item->stock_in_dc }}</td>
                        <td data-label="Vendor (IN)">{{ $item->stock_in_vendor }}</td>
                        <td data-label="Section (IN)">{{ $item->stock_in_section }}</td>
                        <td data-label="GI Plant (OUT)">{{ $item->stock_out_gi_plant }}</td>
                        <td data-label="DC (OUT)">{{ $item->stock_out_dc }}</td>
                        <td data-label="Vendor (OUT)">{{ $item->stock_out_vendor }}</td>
                        <td data-label="Section (OUT)">{{ $item->stock_out_section }}</td>
                        <td data-label="Stock Closing">{{ $item->stock_closing }}</td>
                        <td data-label="Note">{{ $item->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
