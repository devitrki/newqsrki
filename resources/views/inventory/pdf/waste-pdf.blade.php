@extends('reports.templates.pdf.template-multiple')
@section('title')
{{ $title }}
@endsection
@section('content')
    @foreach($data['datas'] as $data)
    <div class="report">
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Plant :</strong> {{ $data['header']['plant_code'] . ' - ' . $data['header']['plant_name'] }}</td>
                </tr>
                <tr>
                    <td><strong>Date :</strong> {{ $data['header']['date_from'] }} - {{ $data['header']['date_until'] }}</td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table>
                <thead>
                <tr>
                    <th>No</th>
                    <th>{{ __('Material Code') }}</th>
                    <th>{{ __('Material Name') }}</th>
                    <th>{{ __('QTY') }}</th>
                    <th>{{ __('Uom') }}</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $total = 0;
                @endphp
                @foreach ($data['items'] as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item->material_code }}</td>
                    <td>{{ $item->material_name }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd(abs($item->qty), '', 3) }}</td>
                    <td>{{ $item->uom }}</td>
                </tr>
                @php
                    $total += $item->qty;
                @endphp
                @endforeach
                <tr>
                    <td colspan="3" data-label="Total Qty" align="right"><b>{{ __('Total Qty') }}</b></td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($total, '', 3) }}</td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@endsection
