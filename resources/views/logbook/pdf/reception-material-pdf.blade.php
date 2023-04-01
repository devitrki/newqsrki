@extends('reports.templates.pdf.template-logbook')
@section('title')
{{ $title }}
@endsection
@section('content')
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td><strong>{{ $data['header']['plant'] }}</strong></td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" width="3%">No</th>
                        <th rowspan="2" width="6%">Date</th>
                        <th rowspan="2" width="14%">Product</th>
                        <th colspan="2" width="8%">Transport</th>
                        <th rowspan="2" width="6%">Product Temp</th>
                        <th rowspan="2" width="10%">Producer</th>
                        <th rowspan="2" width="6%">Country</th>
                        <th rowspan="2" width="10%">Supplier</th>
                        <th rowspan="2" width="6%">Logo Halal</th>
                        <th rowspan="2" width="6%">Product Condition</th>
                        <th rowspan="2" width="7%">Production Code</th>
                        <th rowspan="2" width="4%">Qty</th>
                        <th rowspan="2" width="4%">UOM</th>
                        <th rowspan="2" width="6%">Expired Date</th>
                        <th rowspan="2" width="3%">Status</th>
                        <th rowspan="2" width="6%">PIC</th>
                    </tr>
                    <tr>
                        <td width="4%">Temp</td>
                        <td width="4%">Clean</td>
                    </tr>
                </thead>
                <tbody>
                @foreach ($data['items'] as $i => $item )
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ App\Library\Helper::DateConvertFormat($item->date, 'Y-m-d', 'd-m-Y') }}</td>
                    <td>{{ $item->product }}</td>
                    <td>{{ $item->transport_temperature }}</td>
                    <td>{{ $item->transport_cleanliness }}</td>
                    <td>{{ $item->product_temperature }}</td>
                    <td>{{ $item->producer }}</td>
                    <td>{{ $item->country }}</td>
                    <td>{{ $item->supplier }}</td>
                    <td>{{ $item->logo_halal }}</td>
                    <td>{{ $item->product_condition }}</td>
                    <td>{{ $item->production_code }}</td>
                    <td>{{ $item->product_qty }}</td>
                    <td>{{ $item->product_uom }}</td>
                    <td>{{ App\Library\Helper::DateConvertFormat($item->expired_date, 'Y-m-d', 'd-m-Y') }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->pic }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
