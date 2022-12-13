@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Daily Inventory Cashier') }}</p>
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
                    <th scope="col">Stock In</th>
                    <th scope="col">Stock Out</th>
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
                        <td data-label="Stock In">{{ $item->stock_in }}</td>
                        <td data-label="Stock Out">{{ $item->stock_out }}</td>
                        <td data-label="Stock Closing">{{ $item->stock_closing }}</td>
                        <td data-label="Note">{{ $item->note }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
