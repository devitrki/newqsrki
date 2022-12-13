@extends('reports.templates.view.template1')
@section('content')
    <div class="col-12 border-bottom py-1 title">
        <p class="text-center m-0">{{ __('Reception Material / Product Outlet') }}</p>
    </div>
    <div class="col-12">
        <div class="row head-item-row">
            <div class="col-12 col-md-4 head-item">
                <strong>Outlet :</strong> {{ __($header['plant']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>From Date :</strong> {{ __($header['date_from']) }}
            </div>
            <div class="col-12 col-md-4 head-item">
                <strong>Until Date :</strong> {{ __($header['date_until']) }}
            </div>
        </div>
    </div>
    <div class="col-12 p-0">
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Date</th>
                    <th scope="col">Product</th>
                    <th scope="col">Transport Temperature</th>
                    <th scope="col">Transport Cleanliness</th>
                    <th scope="col">Product Temperature</th>
                    <th scope="col">Producer</th>
                    <th scope="col">Country</th>
                    <th scope="col">Supplier</th>
                    <th scope="col">Logo Halal</th>
                    <th scope="col">Product Condition</th>
                    <th scope="col">Production Code</th>
                    <th scope="col">Qty</th>
                    <th scope="col">UOM</th>
                    <th scope="col">Expired Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">PIC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $i => $item)
                    <tr>
                        <td data-label="No">{{ $i+1 }}</td>
                        <td data-label="Date">{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd-m-Y') }}</td>
                        <td data-label="Product">{{ $item->product }}</td>
                        <td data-label="Transport Temperature">{{ $item->transport_temperature }}</td>
                        <td data-label="Transport Cleanliness">{{ $item->transport_cleanliness }}</td>
                        <td data-label="Product Temperature">{{ $item->product_temperature }}</td>
                        <td data-label="Producer">{{ $item->producer }}</td>
                        <td data-label="Country">{{ $item->country }}</td>
                        <td data-label="Supplier">{{ $item->supplier }}</td>
                        <td data-label="Logo Halal">{{ $item->logo_halal }}</td>
                        <td data-label="Product Condition">{{ $item->product_condition }}</td>
                        <td data-label="Production Code">{{ $item->production_code }}</td>
                        <td data-label="Qty">{{ $item->product_qty }}</td>
                        <td data-label="UOM">{{ $item->product_uom }}</td>
                        <td data-label="Expired Date">{{ App\Library\Helper::DateConvertFormat($item->expired_date, 'Y-m-d', 'd-m-Y') }}</td>
                        <td data-label="Status">{{ $item->status }}</td>
                        <td data-label="PIC">{{ $item->pic }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
