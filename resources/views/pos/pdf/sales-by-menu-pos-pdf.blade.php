@extends('reports.templates.pdf.template1')
@section('content')
    <div class="report">
        <div class="header">
            <table>
                <tr>
                    <td class="title"><h1>{{ $title }}</h1></td>
                </tr>
            </table>
        </div>
        <div class="desc">
            <table>
                <tr>
                    <td><strong>Store :</strong> {{ $data['header']['store'] }}</td>
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
                <th>{{ __('Menu Code') }}</th>
                <th>{{ __('Menu Name') }}</th>
                <th>{{ __('Sale Mode') }}</th>
                <th>{{ __('Qty') }}</th>
                <th>{{ __('Amount') }}</th>
            </tr>
            </thead>
            <tbody>
                @php
                    $totalQty = 0;
                    $totalNetSales = 0;
                @endphp
                @foreach ($data['items'] as $i => $item)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $item->ProductCode }}</td>
                    <td>{{ $item->ProductName }}</td>
                    <td>{{ $item->SaleModeName }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($item->TotalQty, '', 0) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($item->NetSales, '', 2) }}</td>
                </tr>
                @php
                    $totalQty += $item->TotalQty;
                    $totalNetSales += $item->NetSales;
                @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" align="right">Total</th>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($totalQty, '', 0) }}</td>
                    <td align="right">{{ App\Library\Helper::convertNumberToInd($totalNetSales, '', 2) }}</td>
                </tr>
            </tfoot>
        </table>
        </div>
    </div>
@endsection
