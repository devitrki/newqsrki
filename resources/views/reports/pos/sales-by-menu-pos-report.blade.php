@extends('reports.templates.view.template1')
@section('content')
    @if ($status)
        @if ($count > 0)
            <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Sales By Menu POS Report') }}</p>
            <p class="text-center m-0">{{ $header['store'] }}</p>
            <p class="text-center m-0">{{ $header['date_from'] . ' - ' . $header['date_until'] }}</p>
            </div>
            <div class="col-12 p-0">
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">{{ __('Menu Code') }}</th>
                            <th scope="col">{{ __('Menu Name') }}</th>
                            <th scope="col">{{ __('Sale Mode') }}</th>
                            <th scope="col">{{ __('Qty') }}</th>
                            <th scope="col">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQty = 0;
                            $totalNetSales = 0;
                        @endphp
                        @foreach ($items as $i => $item)
                            <tr>
                                <td data-label="No">{{ $i+1 }}</td>
                                <td data-label="{{ __('Menu Code') }}">{{ $item->ProductCode }}</td>
                                <td data-label="{{ __('Menu Name') }}">{{ $item->ProductName }}</td>
                                <td data-label="{{ __('Sale Mode') }}">{{ $item->SaleModeName }}</td>
                                <td data-label="Qty" align="right">{{ App\Library\Helper::convertNumberToInd($item->TotalQty, '', 0) }}</td>
                                <td data-label="Amount" align="right">{{ App\Library\Helper::convertNumberToInd($item->NetSales, '', 0) }}</td>
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
                            <td align="right">{{ App\Library\Helper::convertNumberToInd($totalNetSales, '', 0) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="col-12">
                <h4 class="mt-2 text-center">{{ __('Data Not Found') }}</h4>
            </div>
        @endif
    @else
        <div class="col-12">
            <h4 class="mt-2 text-center">{{ __($message) }}</h4>
        </div>
    @endif
@endsection