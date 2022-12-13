@extends('reports.templates.view.template1')
@section('content')
    @if ($status)
        @if ($count > 0)
            <div class="col-12 border-bottom py-1 title">
            <p class="text-center m-0">{{ __('Sales Per Hour Pos Report') }}</p>
            <p class="text-center m-0">{{ $header['store'] }}</p>
            <p class="text-center m-0">{{ $header['date_from'] . ' - ' . $header['date_until'] }}</p>
            </div>
            <div class="col-12 p-0">
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Hours') }}</th>
                            <th scope="col">{{ __('Bill') }}</th>
                            <th scope="col">{{ __('Customer') }}</th>
                            <th scope="col">{{ __('Total Price') }}</th>
                            <th scope="col">{{ __('Disc') }}</th>
                            <th scope="col">{{ __('Sub Total') }}</th>
                            <th scope="col">{{ __('SC') }}</th>
                            <th scope="col">{{ __('Total Sale') }}</th>
                            <th scope="col">{{ __('Tax') }}</th>
                            <th scope="col">{{ __('Rounding') }}</th>
                            <th scope="col">{{ __('Net Sales') }}</th>
                            <th scope="col">{{ __('Total Payment') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($time = 0; $time <= 23; $time++)
                        <tr>
                        <td data-label="{{ __('Hours') }}">{{ ($time >= 23) ? $time . '-0'  : $time . '-' . ($time + 1) }}</td>
                        @isset( $items['h'.$time] )
                        <td data-label="{{ __('Bill') }}">{{ App\Library\Helper::convertNumberToInd($items['h'.$time]['bill'], '', 0) }}</td>
                        <td data-label="{{ __('Customer') }}">{{ App\Library\Helper::convertNumberToInd($items['h'.$time]['bill'], '', 0) }}
                        <td data-label="{{ __('Total Price') }}">{{ App\Library\Helper::convertNumberToInd($items['h'.$time]['totalPrice'], '', 0) }}
                        <td data-label="{{ __('Disc') }}">{{ App\Library\Helper::convertNumberToInd($items['h'.$time]['disc'], '', 0) }}
                        <td data-label="{{ __('Sub Total') }}">{{ App\Library\Helper::convertNumberToInd($items['h'.$time]['subTotal'], '', 0) }}
                        <td data-label="{{ __('SC') }}">0</td>
                        <td data-label="{{ __('Total Sale') }}">{{ App\Library\Helper::convertNumberToInd($items['h'.$time]['subTotal'], '', 0) }}
                        <td data-label="{{ __('Tax') }}">{{ App\Library\Helper::convertNumberToInd($items['h'.$time]['tax'], '', 0) }}
                        <td data-label="{{ __('Rounding') }}">{{ App\Library\Helper::convertNumberToInd( abs($items['h'.$time]['totalPayment'] - ($items['h'.$time]['subTotal'] + $items['h'.$time]['tax'])), '', 0) }}
                        <td data-label="{{ __('Net Sales') }}">{{ App\Library\Helper::convertNumberToInd($items['h'.$time]['netSales'], '', 0) }}
                        <td data-label="{{ __('Total Payment') }}">{{ App\Library\Helper::convertNumberToInd($items['h'.$time]['totalPayment'], '', 0) }}
                        @else
                        <td data-label="{{ __('Bill') }}">0</td>
                        <td data-label="{{ __('Customer') }}">0</td>
                        <td data-label="{{ __('Total Price') }}">0</td>
                        <td data-label="{{ __('Disc') }}">0</td>
                        <td data-label="{{ __('Sub Total') }}">0</td>
                        <td data-label="{{ __('SC') }}">0</td>
                        <td data-label="{{ __('Total Sale') }}">0</td>
                        <td data-label="{{ __('Tax') }}">0</td>
                        <td data-label="{{ __('Rounding') }}">0</td>
                        <td data-label="{{ __('Net Sales') }}">0</td>
                        <td data-label="{{ __('Total Payment') }}">0</td>
                        @endisset
                        </tr>
                        @endfor
                    </tbody>
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
